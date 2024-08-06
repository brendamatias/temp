<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use App\Models\NotificationChannelModel;
use App\Models\NotificationLogModel;

class EmailService
{
    protected $channelModel;
    protected $logModel;
    protected $email;

    public function __construct()
    {
        $this->channelModel = new NotificationChannelModel();
        $this->logModel = new NotificationLogModel();
        $this->email = \Config\Services::email();
    }

    /**
     * Enviar notificação por e-mail
     */
    public function sendNotification($applicationId, $recipients, $subject, $message, $options = [])
    {
        try {
            // Buscar configuração do canal
            $channel = $this->channelModel->where([
                'application_id' => $applicationId,
                'channel_type' => 'email',
                'is_enabled' => true
            ])->first();

            if (!$channel) {
                throw new \Exception('Canal de e-mail não configurado ou desabilitado');
            }

            $config = json_decode($channel['configuration'], true);
            
            // Configurar e-mail
            $this->configureEmail($config);
            
            // Processar destinatários
            $recipients = is_array($recipients) ? $recipients : [$recipients];
            
            // Verificar limites
            $this->checkLimits($applicationId, count($recipients), $config);
            
            $results = [];
            
            foreach ($recipients as $recipient) {
                try {
                    $result = $this->sendSingleEmail(
                        $recipient,
                        $subject ?: $config['defaults']['subject'],
                        $message,
                        $config,
                        $options
                    );
                    
                    $results[] = [
                        'recipient' => $recipient,
                        'success' => $result['success'],
                        'message_id' => $result['message_id'] ?? null,
                        'error' => $result['error'] ?? null
                    ];
                    
                    // Log da notificação
                    $this->logNotification(
                        $applicationId,
                        'email',
                        $recipient,
                        $subject,
                        $message,
                        $result['success'] ? 'sent' : 'failed',
                        $result['error'] ?? null
                    );
                    
                } catch (\Exception $e) {
                    $results[] = [
                        'recipient' => $recipient,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                    
                    $this->logNotification(
                        $applicationId,
                        'email',
                        $recipient,
                        $subject,
                        $message,
                        'failed',
                        $e->getMessage()
                    );
                }
            }
            
            return [
                'success' => true,
                'results' => $results,
                'total_sent' => count(array_filter($results, fn($r) => $r['success'])),
                'total_failed' => count(array_filter($results, fn($r) => !$r['success']))
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Configurar cliente de e-mail
     */
    private function configureEmail($config)
    {
        $emailConfig = [
            'protocol' => 'smtp',
            'SMTPHost' => $config['smtp']['host'],
            'SMTPPort' => $config['smtp']['port'],
            'SMTPUser' => $config['smtp']['username'],
            'SMTPPass' => $config['smtp']['password'],
            'SMTPCrypto' => $config['smtp']['encryption'] === 'none' ? '' : $config['smtp']['encryption'],
            'SMTPTimeout' => $config['smtp']['timeout'] ?? 30,
            'mailType' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'wordWrap' => true,
            'wrapChars' => 76
        ];
        
        $this->email->initialize($emailConfig);
    }

    /**
     * Enviar e-mail individual
     */
    private function sendSingleEmail($recipient, $subject, $message, $config, $options = [])
    {
        try {
            $this->email->clear();
            
            // Configurar remetente
            $this->email->setFrom(
                $config['from']['email'],
                $config['from']['name']
            );
            
            // Configurar reply-to se definido
            if (!empty($config['reply_to']['email'])) {
                $this->email->setReplyTo(
                    $config['reply_to']['email'],
                    $config['reply_to']['name'] ?: $config['from']['name']
                );
            }
            
            // Configurar destinatário
            if (is_array($recipient)) {
                $this->email->setTo($recipient['email'], $recipient['name'] ?? '');
            } else {
                $this->email->setTo($recipient);
            }
            
            // Configurar assunto
            $this->email->setSubject($subject);
            
            // Processar template
            $template = $options['template'] ?? $config['defaults']['template'] ?? 'default';
            $htmlMessage = $this->processTemplate($template, $message, $config, $options);
            
            $this->email->setMessage($htmlMessage);
            
            // Adicionar headers personalizados
            $this->addCustomHeaders($config, $options);
            
            // Anexos se habilitados
            if ($config['features']['attachments_enabled'] && !empty($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $this->email->attach($attachment['path'], 'attachment', $attachment['name'] ?? null);
                }
            }
            
            // Enviar
            if ($this->email->send()) {
                return [
                    'success' => true,
                    'message_id' => $this->generateMessageId()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $this->email->printDebugger(['headers'])
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Processar template de e-mail
     */
    private function processTemplate($templateType, $message, $config, $options = [])
    {
        $variables = array_merge([
            'message' => $message,
            'app_name' => $options['app_name'] ?? 'Aplicativo',
            'user_name' => $options['user_name'] ?? 'Usuário',
            'date' => date('d/m/Y'),
            'time' => date('H:i'),
            'year' => date('Y')
        ], $options['variables'] ?? []);
        
        switch ($templateType) {
            case 'minimal':
                return $this->getMinimalTemplate($variables, $config);
            case 'modern':
                return $this->getModernTemplate($variables, $config);
            case 'custom':
                return $this->processCustomTemplate($options['custom_template'] ?? '', $variables);
            default:
                return $this->getDefaultTemplate($variables, $config);
        }
    }

    /**
     * Template padrão
     */
    private function getDefaultTemplate($variables, $config)
    {
        $unsubscribeLink = '';
        if ($config['features']['unsubscribe_enabled'] && !empty($config['features']['unsubscribe_url'])) {
            $unsubscribeLink = '<p style="text-align: center; font-size: 12px; color: #666; margin-top: 30px;">
                <a href="' . $config['features']['unsubscribe_url'] . '" style="color: #666;">Cancelar inscrição</a>
            </p>';
        }
        
        $trackingPixel = '';
        if ($config['features']['tracking_enabled']) {
            $trackingPixel = '<img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="1" height="1" style="display: none;" />';
        }
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc($variables['app_name']) . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 30px 20px; text-align: center; }
                .content { padding: 30px 20px; background: #ffffff; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; background: #f8f9fa; }
                .message { background: #f8f9fa; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0; }
                .btn { display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . esc($variables['app_name']) . '</h1>
                </div>
                <div class="content">
                    <div class="message">
                        ' . $variables['message'] . '
                    </div>
                    <p><strong>Data:</strong> ' . $variables['date'] . ' às ' . $variables['time'] . '</p>
                </div>
                <div class="footer">
                    <p>© ' . $variables['year'] . ' ' . esc($variables['app_name']) . '. Todos os direitos reservados.</p>
                    ' . $unsubscribeLink . '
                </div>
            </div>
            ' . $trackingPixel . '
        </body>
        </html>
        ';
    }

    /**
     * Template minimalista
     */
    private function getMinimalTemplate($variables, $config)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 40px 20px; }
                .container { max-width: 500px; margin: 0 auto; }
                .message { margin: 20px 0; }
                .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="message">
                    ' . $variables['message'] . '
                </div>
                <div class="footer">
                    <p>' . esc($variables['app_name']) . ' • ' . $variables['date'] . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Template moderno
     */
    private function getModernTemplate($variables, $config)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: "Inter", -apple-system, BlinkMacSystemFont, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 0; background: #f9fafb; }
                .container { max-width: 600px; margin: 0 auto; background: white; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 30px; text-align: center; }
                .content { padding: 40px 30px; }
                .message { background: #f8fafc; padding: 24px; border-radius: 8px; border-left: 4px solid #667eea; }
                .footer { padding: 30px; text-align: center; background: #f8fafc; color: #6b7280; font-size: 14px; }
                .badge { display: inline-block; background: #10b981; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">' . esc($variables['app_name']) . '</h1>
                    <div class="badge" style="margin-top: 12px;">Nova Notificação</div>
                </div>
                <div class="content">
                    <div class="message">
                        ' . $variables['message'] . '
                    </div>
                    <p style="margin-top: 24px; color: #6b7280;">Recebido em ' . $variables['date'] . ' às ' . $variables['time'] . '</p>
                </div>
                <div class="footer">
                    <p>© ' . $variables['year'] . ' ' . esc($variables['app_name']) . '</p>
                </div>
            </div>
        </body>
        </html>
        ';
    }

    /**
     * Processar template personalizado
     */
    private function processCustomTemplate($template, $variables)
    {
        $processedTemplate = $template;
        
        foreach ($variables as $key => $value) {
            $processedTemplate = str_replace('{{' . $key . '}}', $value, $processedTemplate);
        }
        
        return $processedTemplate;
    }

    /**
     * Adicionar headers personalizados
     */
    private function addCustomHeaders($config, $options)
    {
        // Message-ID único
        $messageId = $this->generateMessageId();
        $this->email->setHeader('Message-ID', $messageId);
        
        // Headers de tracking se habilitado
        if ($config['features']['tracking_enabled']) {
            $this->email->setHeader('X-Track-Opens', 'true');
        }
        
        // Headers personalizados
        if (!empty($options['headers'])) {
            foreach ($options['headers'] as $name => $value) {
                $this->email->setHeader($name, $value);
            }
        }
    }

    /**
     * Verificar limites de envio
     */
    private function checkLimits($applicationId, $recipientCount, $config)
    {
        // Verificar limite de destinatários por envio
        if ($recipientCount > $config['limits']['max_recipients']) {
            throw new \Exception('Limite de destinatários excedido: ' . $recipientCount . '/' . $config['limits']['max_recipients']);
        }
        
        // Verificar limite diário (simplificado)
        $today = date('Y-m-d');
        $dailyCount = $this->logModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'email',
            'DATE(created_at)' => $today
        ])->countAllResults();
        
        if ($dailyCount >= $config['limits']['daily_limit']) {
            throw new \Exception('Limite diário de e-mails excedido: ' . $dailyCount . '/' . $config['limits']['daily_limit']);
        }
        
        // Verificar limite por hora (simplificado)
        $currentHour = date('Y-m-d H:00:00');
        $hourlyCount = $this->logModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'email',
            'created_at >=' => $currentHour
        ])->countAllResults();
        
        if ($hourlyCount >= $config['limits']['hourly_limit']) {
            throw new \Exception('Limite por hora de e-mails excedido: ' . $hourlyCount . '/' . $config['limits']['hourly_limit']);
        }
    }

    /**
     * Gerar Message-ID único
     */
    private function generateMessageId()
    {
        return '<' . uniqid() . '@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '>';
    }

    /**
     * Log da notificação
     */
    private function logNotification($applicationId, $channelType, $recipient, $subject, $message, $status, $error = null)
    {
        $this->logModel->insert([
            'application_id' => $applicationId,
            'channel_type' => $channelType,
            'recipient' => is_array($recipient) ? $recipient['email'] : $recipient,
            'subject' => $subject,
            'message' => $message,
            'status' => $status,
            'error_message' => $error,
            'sent_at' => $status === 'sent' ? date('Y-m-d H:i:s') : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Enviar e-mail de boas-vindas
     */
    public function sendWelcomeEmail($applicationId, $recipient, $options = [])
    {
        try {
            $channel = $this->channelModel->where([
                'application_id' => $applicationId,
                'channel_type' => 'email',
                'is_enabled' => true
            ])->first();

            if (!$channel) {
                throw new \Exception('Canal de e-mail não configurado');
            }

            $config = json_decode($channel['configuration'], true);
            
            if (!$config['welcome']['enabled']) {
                return ['success' => false, 'error' => 'E-mail de boas-vindas não habilitado'];
            }
            
            $this->configureEmail($config);
            
            $welcomeOptions = array_merge($options, [
                'template' => 'custom',
                'custom_template' => $config['welcome']['template']
            ]);
            
            return $this->sendSingleEmail(
                $recipient,
                $config['welcome']['subject'],
                $config['welcome']['template'],
                $config,
                $welcomeOptions
            );
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validar configuração de e-mail
     */
    public function validateConfiguration($config)
    {
        $required = [
            'smtp.host',
            'smtp.port',
            'smtp.username',
            'smtp.password',
            'from.email',
            'from.name'
        ];
        
        foreach ($required as $field) {
            $keys = explode('.', $field);
            $value = $config;
            
            foreach ($keys as $key) {
                if (!isset($value[$key])) {
                    return ['valid' => false, 'error' => "Campo obrigatório: {$field}"];
                }
                $value = $value[$key];
            }
            
            if (empty($value)) {
                return ['valid' => false, 'error' => "Campo obrigatório: {$field}"];
            }
        }
        
        // Validar e-mails
        if (!filter_var($config['smtp']['username'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'E-mail SMTP inválido'];
        }
        
        if (!filter_var($config['from']['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'E-mail do remetente inválido'];
        }
        
        // Validar porta
        $port = (int) $config['smtp']['port'];
        if ($port < 1 || $port > 65535) {
            return ['valid' => false, 'error' => 'Porta SMTP inválida'];
        }
        
        return ['valid' => true];
    }
}