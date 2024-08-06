<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationLogModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class NotificationController extends BaseController
{
    protected $applicationModel;
    protected $logModel;
    
    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->logModel = new NotificationLogModel();
    }
    
    /**
     * Exibir histórico de notificações
     */
    public function history(int $applicationId)
    {
        // Verificar se o usuário tem acesso ao aplicativo
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();
        
        if (!$application) {
            return redirect()->to('/dashboard')->with('error', 'Aplicativo não encontrado.');
        }
        
        // Parâmetros de filtro
        $filters = [
            'channel' => $this->request->getGet('channel'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'recipient' => $this->request->getGet('recipient'),
            'search' => $this->request->getGet('search')
        ];
        
        // Paginação
        $perPage = $this->request->getGet('per_page') ?? 25;
        $page = $this->request->getGet('page') ?? 1;
        
        // Buscar notificações com filtros
        $query = $this->logModel->where('application_id', $applicationId);
        
        // Aplicar filtros
        if ($filters['channel']) {
            $query->where('channel_type', $filters['channel']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_from']) {
            $query->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        
        if ($filters['date_to']) {
            $query->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        
        if ($filters['recipient']) {
            $query->like('recipient', $filters['recipient']);
        }
        
        if ($filters['search']) {
            $query->groupStart()
                  ->like('subject', $filters['search'])
                  ->orLike('message', $filters['search'])
                  ->orLike('recipient', $filters['search'])
                  ->groupEnd();
        }
        
        // Ordenar por data mais recente
        $query->orderBy('created_at', 'DESC');
        
        // Paginação
        $notifications = $query->paginate($perPage, 'default', $page);
        $pager = $this->logModel->pager;
        
        // Estatísticas
        $stats = $this->getNotificationStats($applicationId, $filters);
        
        $data = [
            'application' => $application,
            'notifications' => $notifications,
            'pager' => $pager,
            'filters' => $filters,
            'stats' => $stats,
            'per_page' => $perPage,
            'channels' => ['webpush' => 'Web Push', 'email' => 'E-mail', 'sms' => 'SMS'],
            'statuses' => ['sent' => 'Enviado', 'failed' => 'Falhou', 'pending' => 'Pendente', 'delivered' => 'Entregue', 'opened' => 'Aberto', 'clicked' => 'Clicado']
        ];
        
        return view('notifications/history', $data);
    }
    
    /**
     * Exportar histórico para PDF
     */
    public function exportPdf(int $applicationId)
    {
        // Verificar acesso
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();
        
        if (!$application) {
            return redirect()->to('/dashboard')->with('error', 'Aplicativo não encontrado.');
        }
        
        // Aplicar mesmos filtros da tela
        $filters = [
            'channel' => $this->request->getGet('channel'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'recipient' => $this->request->getGet('recipient'),
            'search' => $this->request->getGet('search')
        ];
        
        $query = $this->logModel->where('application_id', $applicationId);
        
        // Aplicar filtros (mesmo código da função history)
        if ($filters['channel']) {
            $query->where('channel_type', $filters['channel']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_from']) {
            $query->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        
        if ($filters['date_to']) {
            $query->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        
        if ($filters['recipient']) {
            $query->like('recipient', $filters['recipient']);
        }
        
        if ($filters['search']) {
            $query->groupStart()
                  ->like('subject', $filters['search'])
                  ->orLike('message', $filters['search'])
                  ->orLike('recipient', $filters['search'])
                  ->groupEnd();
        }
        
        $notifications = $query->orderBy('created_at', 'DESC')->findAll(1000); // Limite de 1000 registros
        $stats = $this->getNotificationStats($applicationId, $filters);
        
        // Gerar PDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = view('notifications/pdf_export', [
            'application' => $application,
            'notifications' => $notifications,
            'stats' => $stats,
            'filters' => $filters,
            'generated_at' => date('d/m/Y H:i:s')
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'historico_notificacoes_' . $application['name'] . '_' . date('Y-m-d') . '.pdf';
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
    
    /**
     * Exportar histórico para Excel
     */
    public function exportExcel(int $applicationId)
    {
        // Verificar acesso
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();
        
        if (!$application) {
            return redirect()->to('/dashboard')->with('error', 'Aplicativo não encontrado.');
        }
        
        // Aplicar filtros
        $filters = [
            'channel' => $this->request->getGet('channel'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'recipient' => $this->request->getGet('recipient'),
            'search' => $this->request->getGet('search')
        ];
        
        $query = $this->logModel->where('application_id', $applicationId);
        
        // Aplicar filtros (mesmo código)
        if ($filters['channel']) {
            $query->where('channel_type', $filters['channel']);
        }
        
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        
        if ($filters['date_from']) {
            $query->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        
        if ($filters['date_to']) {
            $query->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        
        if ($filters['recipient']) {
            $query->like('recipient', $filters['recipient']);
        }
        
        if ($filters['search']) {
            $query->groupStart()
                  ->like('subject', $filters['search'])
                  ->orLike('message', $filters['search'])
                  ->orLike('recipient', $filters['search'])
                  ->groupEnd();
        }
        
        $notifications = $query->orderBy('created_at', 'DESC')->findAll(5000); // Limite de 5000 registros
        
        // Criar planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Cabeçalhos
        $headers = ['Data/Hora', 'Canal', 'Destinatário', 'Assunto', 'Mensagem', 'Status', 'Enviado em'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Estilizar cabeçalhos
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => 'center']
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        
        // Dados
        $row = 2;
        foreach ($notifications as $notification) {
            $sheet->setCellValue('A' . $row, date('d/m/Y H:i:s', strtotime($notification['created_at'])));
            $sheet->setCellValue('B' . $row, ucfirst($notification['channel_type']));
            $sheet->setCellValue('C' . $row, $notification['recipient']);
            $sheet->setCellValue('D' . $row, $notification['subject'] ?? '-');
            $sheet->setCellValue('E' . $row, substr($notification['message'], 0, 100) . (strlen($notification['message']) > 100 ? '...' : ''));
            $sheet->setCellValue('F' . $row, $this->getStatusLabel($notification['status']));
            $sheet->setCellValue('G' . $row, $notification['sent_at'] ? date('d/m/Y H:i:s', strtotime($notification['sent_at'])) : '-');
            $row++;
        }
        
        // Ajustar largura das colunas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Adicionar filtros
        $sheet->setAutoFilter('A1:G' . ($row - 1));
        
        // Gerar arquivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'historico_notificacoes_' . $application['name'] . '_' . date('Y-m-d') . '.xlsx';
        
        // Salvar em buffer
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
    
    /**
     * Formulário de envio manual
     */
    public function sendForm(int $applicationId)
    {
        // Verificar acesso
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();
        
        if (!$application) {
            return redirect()->to('/dashboard')->with('error', 'Aplicativo não encontrado.');
        }
        
        // Buscar canais configurados
        $channelModel = new \App\Models\NotificationChannelModel();
        $channels = $channelModel->where([
            'application_id' => $applicationId,
            'is_enabled' => 1
        ])->findAll();
        
        $data = [
            'application' => $application,
            'channels' => $channels
        ];
        
        return view('notifications/send_form', $data);
    }
    
    /**
     * Processar envio manual
     */
    public function send(int $applicationId)
    {
        // Verificar acesso
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();
        
        if (!$application) {
            return redirect()->to('/dashboard')->with('error', 'Aplicativo não encontrado.');
        }
        
        // Validar dados
        $rules = [
            'channel' => 'required|in_list[webpush,email,sms]',
            'recipients' => 'required',
            'message' => 'required|min_length[1]|max_length[1000]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $channel = $this->request->getPost('channel');
        $recipients = $this->request->getPost('recipients');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');
        $scheduleTime = $this->request->getPost('schedule_time');
        
        // Processar lista de destinatários
        $recipientList = [];
        if (is_string($recipients)) {
            $recipientList = array_filter(array_map('trim', explode('\n', $recipients)));
        }
        
        if (empty($recipientList)) {
            return redirect()->back()->withInput()->with('error', 'Lista de destinatários inválida.');
        }
        
        try {
            // Enviar notificações
            $results = $this->sendNotifications($applicationId, $channel, $recipientList, $subject, $message, $scheduleTime);
            
            $successCount = $results['success'];
            $failureCount = $results['failed'];
            
            if ($successCount > 0) {
                $message = "Notificações enviadas com sucesso: {$successCount}";
                if ($failureCount > 0) {
                    $message .= ", falharam: {$failureCount}";
                }
                return redirect()->to("/applications/{$applicationId}/history")->with('success', $message);
            } else {
                return redirect()->back()->withInput()->with('error', 'Nenhuma notificação foi enviada com sucesso.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erro ao enviar notificações: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter estatísticas de notificações
     */
    protected function getNotificationStats(int $applicationId, array $filters = []): array
    {
        $baseQuery = $this->logModel->where('application_id', $applicationId);
        
        // Aplicar filtros de data se especificados
        if ($filters['date_from'] ?? false) {
            $baseQuery->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        
        if ($filters['date_to'] ?? false) {
            $baseQuery->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        
        // Total de notificações
        $total = $baseQuery->countAllResults(false);
        
        // Por status
        $sent = $baseQuery->where('status', 'sent')->countAllResults(false);
        $failed = $baseQuery->where('status', 'failed')->countAllResults(false);
        $pending = $baseQuery->where('status', 'pending')->countAllResults(false);
        $delivered = $baseQuery->where('status', 'delivered')->countAllResults(false);
        
        // Por canal
        $webpush = $this->logModel->where('application_id', $applicationId)->where('channel_type', 'webpush')->countAllResults();
        $email = $this->logModel->where('application_id', $applicationId)->where('channel_type', 'email')->countAllResults();
        $sms = $this->logModel->where('application_id', $applicationId)->where('channel_type', 'sms')->countAllResults();
        
        // Taxa de sucesso
        $successRate = $total > 0 ? round(($sent / $total) * 100, 2) : 0;
        
        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'delivered' => $delivered,
            'success_rate' => $successRate,
            'by_channel' => [
                'webpush' => $webpush,
                'email' => $email,
                'sms' => $sms
            ]
        ];
    }
    
    /**
     * Enviar notificações por canal
     */
    protected function sendNotifications(int $applicationId, string $channel, array $recipients, ?string $subject, string $message, ?string $scheduleTime = null): array
    {
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($recipients as $recipient) {
            try {
                switch ($channel) {
                    case 'webpush':
                        $service = new \App\Libraries\WebPushService();
                        $service->configure($applicationId);
                        $result = $service->sendNotification($recipient, $message, [
                            'title' => $subject ?? 'Nova Notificação',
                            'url' => '/'
                        ]);
                        break;
                        
                    case 'email':
                        $service = new \App\Libraries\EmailService();
                        $service->configure($applicationId);
                        $result = $service->sendEmail($recipient, $subject ?? 'Nova Notificação', $message);
                        break;
                        
                    case 'sms':
                        $service = new \App\Libraries\SmsService();
                        $service->configure($applicationId);
                        $result = $service->sendSms($recipient, $message);
                        break;
                        
                    default:
                        throw new \Exception('Canal não suportado: ' . $channel);
                }
                
                if ($result['success'] ?? false) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
                
            } catch (\Exception $e) {
                $failureCount++;
                log_message('error', 'Erro ao enviar notificação: ' . $e->getMessage());
            }
        }
        
        return [
            'success' => $successCount,
            'failed' => $failureCount
        ];
    }
    
    /**
     * Obter label do status
     */
    protected function getStatusLabel(string $status): string
    {
        $labels = [
            'sent' => 'Enviado',
            'failed' => 'Falhou',
            'pending' => 'Pendente',
            'delivered' => 'Entregue',
            'opened' => 'Aberto',
            'clicked' => 'Clicado'
        ];
        
        return $labels[$status] ?? ucfirst($status);
    }
}