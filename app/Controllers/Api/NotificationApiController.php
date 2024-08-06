<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ApplicationModel;
use App\Models\NotificationChannelModel;
use App\Models\NotificationLogModel;
use App\Libraries\WebPushService;
use App\Libraries\EmailService;
use App\Libraries\SmsService;

class NotificationApiController extends ResourceController
{
    use ResponseTrait;

    protected $applicationModel;
    protected $channelModel;
    protected $logModel;
    protected $webPushService;
    protected $emailService;
    protected $smsService;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->channelModel = new NotificationChannelModel();
        $this->logModel = new NotificationLogModel();
        $this->webPushService = new WebPushService();
        $this->emailService = new EmailService();
        $this->smsService = new SmsService();
    }

    /**
     * Autenticar aplicação via API Key
     */
    protected function authenticateApplication()
    {
        $apiKey = $this->request->getHeaderLine('X-API-Key') ?: $this->request->getGet('api_key');
        
        if (!$apiKey) {
            return $this->failUnauthorized('API Key é obrigatória');
        }

        $application = $this->applicationModel->where('api_key', $apiKey)
                                            ->where('status', 'active')
                                            ->first();

        if (!$application) {
            return $this->failUnauthorized('API Key inválida ou aplicação inativa');
        }

        return $application;
    }

    /**
     * Enviar notificação via API
     * POST /api/notifications/send
     */
    public function send()
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application; // Retorna erro de autenticação
        }

        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        // Validação dos dados
        $validation = \Config\Services::validation();
        $validation->setRules([
            'channel' => 'required|in_list[webpush,email,sms]',
            'recipients' => 'required|is_array',
            'message' => 'required|string|max_length[1000]',
            'subject' => 'permit_empty|string|max_length[200]',
            'priority' => 'permit_empty|in_list[low,normal,high]',
            'schedule_at' => 'permit_empty|valid_date'
        ]);

        if (!$validation->run($data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $channel = $data['channel'];
        $recipients = $data['recipients'];
        $message = $data['message'];
        $subject = $data['subject'] ?? null;
        $priority = $data['priority'] ?? 'normal';
        $scheduleAt = $data['schedule_at'] ?? null;

        // Verificar se o canal está configurado
        $channelConfig = $this->channelModel->where('application_id', $application['id'])
                                          ->where('channel_type', $channel)
                                          ->where('is_active', 1)
                                          ->first();

        if (!$channelConfig) {
            return $this->failNotFound("Canal {$channel} não está configurado ou ativo");
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($recipients as $recipient) {
            try {
                $notificationData = [
                    'application_id' => $application['id'],
                    'channel_type' => $channel,
                    'recipient' => $recipient,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => $scheduleAt ? 'scheduled' : 'pending',
                    'priority' => $priority,
                    'scheduled_at' => $scheduleAt,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Inserir no log
                $notificationId = $this->logModel->insert($notificationData);

                if (!$scheduleAt) {
                    // Enviar imediatamente
                    $sendResult = $this->sendNotification($channel, $application, $recipient, $message, $subject, $data);
                    
                    if ($sendResult['success']) {
                        $this->logModel->update($notificationId, [
                            'status' => 'sent',
                            'sent_at' => date('Y-m-d H:i:s'),
                            'provider_response' => json_encode($sendResult['response'] ?? [])
                        ]);
                        $successCount++;
                    } else {
                        $this->logModel->update($notificationId, [
                            'status' => 'failed',
                            'error_message' => $sendResult['error'],
                            'provider_response' => json_encode($sendResult['response'] ?? [])
                        ]);
                        $failureCount++;
                    }

                    $results[] = [
                        'recipient' => $recipient,
                        'status' => $sendResult['success'] ? 'sent' : 'failed',
                        'message' => $sendResult['success'] ? 'Enviado com sucesso' : $sendResult['error'],
                        'notification_id' => $notificationId
                    ];
                } else {
                    $results[] = [
                        'recipient' => $recipient,
                        'status' => 'scheduled',
                        'message' => 'Agendado para ' . $scheduleAt,
                        'notification_id' => $notificationId
                    ];
                }
            } catch (\Exception $e) {
                $failureCount++;
                $results[] = [
                    'recipient' => $recipient,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'notification_id' => null
                ];
            }
        }

        return $this->respond([
            'success' => true,
            'message' => "Processamento concluído: {$successCount} enviadas, {$failureCount} falharam",
            'summary' => [
                'total' => count($recipients),
                'success' => $successCount,
                'failed' => $failureCount,
                'scheduled' => $scheduleAt ? count($recipients) : 0
            ],
            'results' => $results
        ]);
    }

    /**
     * Enviar notificação específica
     */
    private function sendNotification($channel, $application, $recipient, $message, $subject = null, $extraData = [])
    {
        try {
            switch ($channel) {
                case 'webpush':
                    $this->webPushService->setApplication($application['id']);
                    $payload = [
                        'title' => $subject ?: $application['name'],
                        'body' => $message,
                        'icon' => $extraData['icon'] ?? null,
                        'badge' => $extraData['badge'] ?? null,
                        'url' => $extraData['url'] ?? null
                    ];
                    return $this->webPushService->sendNotification($recipient, $payload);

                case 'email':
                    $this->emailService->setApplication($application['id']);
                    return $this->emailService->sendEmail(
                        $recipient,
                        $subject ?: 'Notificação',
                        $message,
                        $extraData['template'] ?? 'default',
                        $extraData['sender_name'] ?? null
                    );

                case 'sms':
                    $this->smsService->setApplication($application['id']);
                    return $this->smsService->sendSms(
                        $recipient,
                        $message,
                        $extraData['sender'] ?? null,
                        $extraData['unicode'] ?? false,
                        $extraData['flash'] ?? false
                    );

                default:
                    return ['success' => false, 'error' => 'Canal não suportado'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obter histórico de notificações
     * GET /api/notifications/history
     */
    public function history()
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = min((int) ($this->request->getGet('per_page') ?? 50), 100);
        $channel = $this->request->getGet('channel');
        $status = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $recipient = $this->request->getGet('recipient');

        $builder = $this->logModel->where('application_id', $application['id']);

        if ($channel) {
            $builder->where('channel_type', $channel);
        }

        if ($status) {
            $builder->where('status', $status);
        }

        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('created_at <=', $dateTo . ' 23:59:59');
        }

        if ($recipient) {
            $builder->like('recipient', $recipient);
        }

        $total = $builder->countAllResults(false);
        $notifications = $builder->orderBy('created_at', 'DESC')
                               ->limit($perPage, ($page - 1) * $perPage)
                               ->get()
                               ->getResultArray();

        return $this->respond([
            'success' => true,
            'data' => $notifications,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => ceil($total / $perPage)
            ]
        ]);
    }

    /**
     * Obter estatísticas
     * GET /api/notifications/stats
     */
    public function stats()
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        $period = $this->request->getGet('period') ?? '30d'; // 1d, 7d, 30d, 90d
        
        $dateFrom = match($period) {
            '1d' => date('Y-m-d', strtotime('-1 day')),
            '7d' => date('Y-m-d', strtotime('-7 days')),
            '30d' => date('Y-m-d', strtotime('-30 days')),
            '90d' => date('Y-m-d', strtotime('-90 days')),
            default => date('Y-m-d', strtotime('-30 days'))
        };

        $builder = $this->logModel->where('application_id', $application['id'])
                                ->where('created_at >=', $dateFrom . ' 00:00:00');

        $total = $builder->countAllResults(false);
        $sent = $builder->where('status', 'sent')->countAllResults(false);
        $failed = $builder->where('status', 'failed')->countAllResults(false);
        $pending = $builder->where('status', 'pending')->countAllResults(false);
        $delivered = $builder->where('status', 'delivered')->countAllResults(false);

        // Estatísticas por canal
        $channelStats = [];
        $channels = ['webpush', 'email', 'sms'];
        
        foreach ($channels as $channel) {
            $channelBuilder = $this->logModel->where('application_id', $application['id'])
                                           ->where('channel_type', $channel)
                                           ->where('created_at >=', $dateFrom . ' 00:00:00');
            
            $channelTotal = $channelBuilder->countAllResults(false);
            $channelSent = $channelBuilder->where('status', 'sent')->countAllResults(false);
            
            $channelStats[$channel] = [
                'total' => $channelTotal,
                'sent' => $channelSent,
                'success_rate' => $channelTotal > 0 ? round(($channelSent / $channelTotal) * 100, 2) : 0
            ];
        }

        // Estatísticas por dia (últimos 7 dias)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayBuilder = $this->logModel->where('application_id', $application['id'])
                                       ->where('DATE(created_at)', $date);
            
            $dayTotal = $dayBuilder->countAllResults(false);
            $daySent = $dayBuilder->where('status', 'sent')->countAllResults(false);
            
            $dailyStats[] = [
                'date' => $date,
                'total' => $dayTotal,
                'sent' => $daySent
            ];
        }

        return $this->respond([
            'success' => true,
            'period' => $period,
            'summary' => [
                'total' => $total,
                'sent' => $sent,
                'failed' => $failed,
                'pending' => $pending,
                'delivered' => $delivered,
                'success_rate' => $total > 0 ? round(($sent / $total) * 100, 2) : 0
            ],
            'by_channel' => $channelStats,
            'daily' => $dailyStats
        ]);
    }

    /**
     * Obter detalhes de uma notificação
     * GET /api/notifications/{id}
     */
    public function show($id = null)
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        if (!$id) {
            return $this->failValidationErrors('ID da notificação é obrigatório');
        }

        $notification = $this->logModel->where('id', $id)
                                     ->where('application_id', $application['id'])
                                     ->first();

        if (!$notification) {
            return $this->failNotFound('Notificação não encontrada');
        }

        return $this->respond([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Reenviar notificação falhada
     * POST /api/notifications/{id}/retry
     */
    public function retry($id = null)
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        if (!$id) {
            return $this->failValidationErrors('ID da notificação é obrigatório');
        }

        $notification = $this->logModel->where('id', $id)
                                     ->where('application_id', $application['id'])
                                     ->where('status', 'failed')
                                     ->first();

        if (!$notification) {
            return $this->failNotFound('Notificação não encontrada ou não está com status de falha');
        }

        try {
            $sendResult = $this->sendNotification(
                $notification['channel_type'],
                $application,
                $notification['recipient'],
                $notification['message'],
                $notification['subject']
            );

            if ($sendResult['success']) {
                $this->logModel->update($id, [
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                    'error_message' => null,
                    'provider_response' => json_encode($sendResult['response'] ?? [])
                ]);

                return $this->respond([
                    'success' => true,
                    'message' => 'Notificação reenviada com sucesso'
                ]);
            } else {
                $this->logModel->update($id, [
                    'error_message' => $sendResult['error'],
                    'provider_response' => json_encode($sendResult['response'] ?? [])
                ]);

                return $this->fail([
                    'success' => false,
                    'error' => 'Falha ao reenviar: ' . $sendResult['error']
                ]);
            }
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'error' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obter configurações dos canais
     * GET /api/channels
     */
    public function channels()
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        $channels = $this->channelModel->where('application_id', $application['id'])
                                     ->get()
                                     ->getResultArray();

        $result = [];
        foreach ($channels as $channel) {
            $result[$channel['channel_type']] = [
                'type' => $channel['channel_type'],
                'active' => (bool) $channel['is_active'],
                'configured' => !empty($channel['configuration']),
                'updated_at' => $channel['updated_at']
            ];
        }

        return $this->respond([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Webhook para receber atualizações de status
     * POST /api/webhooks/status
     */
    public function webhook()
    {
        $application = $this->authenticateApplication();
        if (!is_array($application)) {
            return $application;
        }

        $data = $this->request->getJSON(true) ?: $this->request->getPost();

        if (!isset($data['notification_id']) || !isset($data['status'])) {
            return $this->failValidationErrors('notification_id e status são obrigatórios');
        }

        $notificationId = $data['notification_id'];
        $status = $data['status'];
        $timestamp = $data['timestamp'] ?? date('Y-m-d H:i:s');
        $providerResponse = $data['provider_response'] ?? null;

        // Validar status
        $validStatuses = ['delivered', 'opened', 'clicked', 'bounced', 'complained'];
        if (!in_array($status, $validStatuses)) {
            return $this->failValidationErrors('Status inválido');
        }

        // Verificar se a notificação existe e pertence à aplicação
        $notification = $this->logModel->where('id', $notificationId)
                                     ->where('application_id', $application['id'])
                                     ->first();

        if (!$notification) {
            return $this->failNotFound('Notificação não encontrada');
        }

        // Atualizar status
        $updateData = [
            'status' => $status,
            'updated_at' => $timestamp
        ];

        if ($status === 'delivered' && !$notification['delivered_at']) {
            $updateData['delivered_at'] = $timestamp;
        }

        if ($status === 'opened' && !$notification['opened_at']) {
            $updateData['opened_at'] = $timestamp;
        }

        if ($status === 'clicked' && !$notification['clicked_at']) {
            $updateData['clicked_at'] = $timestamp;
        }

        if ($providerResponse) {
            $updateData['provider_response'] = json_encode($providerResponse);
        }

        $this->logModel->update($notificationId, $updateData);

        return $this->respond([
            'success' => true,
            'message' => 'Status atualizado com sucesso'
        ]);
    }

    /**
     * Documentação da API
     * GET /api/docs
     */
    public function docs()
    {
        $docs = [
            'version' => '1.0.0',
            'base_url' => base_url('api'),
            'authentication' => [
                'type' => 'API Key',
                'header' => 'X-API-Key',
                'parameter' => 'api_key'
            ],
            'endpoints' => [
                [
                    'method' => 'POST',
                    'path' => '/notifications/send',
                    'description' => 'Enviar notificação',
                    'parameters' => [
                        'channel' => 'string (webpush|email|sms)',
                        'recipients' => 'array',
                        'message' => 'string',
                        'subject' => 'string (opcional)',
                        'priority' => 'string (low|normal|high)',
                        'schedule_at' => 'datetime (opcional)'
                    ]
                ],
                [
                    'method' => 'GET',
                    'path' => '/notifications/history',
                    'description' => 'Obter histórico de notificações',
                    'parameters' => [
                        'page' => 'integer',
                        'per_page' => 'integer (max 100)',
                        'channel' => 'string',
                        'status' => 'string',
                        'date_from' => 'date',
                        'date_to' => 'date',
                        'recipient' => 'string'
                    ]
                ],
                [
                    'method' => 'GET',
                    'path' => '/notifications/stats',
                    'description' => 'Obter estatísticas',
                    'parameters' => [
                        'period' => 'string (1d|7d|30d|90d)'
                    ]
                ],
                [
                    'method' => 'GET',
                    'path' => '/notifications/{id}',
                    'description' => 'Obter detalhes de uma notificação'
                ],
                [
                    'method' => 'POST',
                    'path' => '/notifications/{id}/retry',
                    'description' => 'Reenviar notificação falhada'
                ],
                [
                    'method' => 'GET',
                    'path' => '/channels',
                    'description' => 'Obter configurações dos canais'
                ],
                [
                    'method' => 'POST',
                    'path' => '/webhooks/status',
                    'description' => 'Webhook para atualizações de status',
                    'parameters' => [
                        'notification_id' => 'integer',
                        'status' => 'string (delivered|opened|clicked|bounced|complained)',
                        'timestamp' => 'datetime (opcional)',
                        'provider_response' => 'object (opcional)'
                    ]
                ]
            ],
            'response_format' => [
                'success' => [
                    'success' => true,
                    'data' => 'mixed',
                    'message' => 'string (opcional)'
                ],
                'error' => [
                    'success' => false,
                    'error' => 'string',
                    'details' => 'mixed (opcional)'
                ]
            ]
        ];

        return $this->respond($docs);
    }
}