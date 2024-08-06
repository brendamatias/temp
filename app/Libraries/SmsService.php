<?php

namespace App\Libraries;

use App\Models\NotificationChannelModel;
use App\Models\NotificationLogModel;
use CodeIgniter\HTTP\CURLRequest;
use Exception;

class SmsService
{
    protected $channelModel;
    protected $logModel;
    protected $config;
    protected $applicationId;
    
    public function __construct()
    {
        $this->channelModel = new NotificationChannelModel();
        $this->logModel = new NotificationLogModel();
    }
    
    /**
     * Configurar o serviço para uma aplicação específica
     */
    public function configure(int $applicationId): bool
    {
        $this->applicationId = $applicationId;
        
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms',
            'is_enabled' => 1
        ])->first();
        
        if (!$channel) {
            throw new Exception('Canal SMS não configurado ou desabilitado');
        }
        
        $this->config = json_decode($channel['configuration'], true);
        
        return true;
    }
    
    /**
     * Enviar SMS
     */
    public function sendSms(string $to, string $message, array $options = []): array
    {
        if (!$this->config) {
            throw new Exception('Serviço SMS não configurado');
        }
        
        // Verificar limites de envio
        if (!$this->checkSendingLimits()) {
            throw new Exception('Limite de envio excedido');
        }
        
        // Processar mensagem
        $processedMessage = $this->processMessage($message, $options);
        
        // Validar número de telefone
        $to = $this->validatePhoneNumber($to);
        
        try {
            $result = $this->sendByProvider($to, $processedMessage, $options);
            
            // Registrar log de sucesso
            $this->logNotification($to, $processedMessage, 'sent', $result);
            
            return [
                'success' => true,
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result
            ];
            
        } catch (Exception $e) {
            // Registrar log de erro
            $this->logNotification($to, $processedMessage, 'failed', ['error' => $e->getMessage()]);
            
            throw $e;
        }
    }
    
    /**
     * Enviar SMS para múltiplos destinatários
     */
    public function sendBulkSms(array $recipients, string $message, array $options = []): array
    {
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        foreach ($recipients as $recipient) {
            try {
                $result = $this->sendSms($recipient, $message, $options);
                $results[] = [
                    'recipient' => $recipient,
                    'status' => 'success',
                    'result' => $result
                ];
                $successCount++;
            } catch (Exception $e) {
                $results[] = [
                    'recipient' => $recipient,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                $failureCount++;
            }
        }
        
        return [
            'total' => count($recipients),
            'success' => $successCount,
            'failed' => $failureCount,
            'results' => $results
        ];
    }
    
    /**
     * Enviar SMS de boas-vindas
     */
    public function sendWelcomeSms(string $to, array $userData = []): array
    {
        if (!$this->config['welcome']['enabled'] ?? false) {
            return ['success' => false, 'message' => 'SMS de boas-vindas desabilitado'];
        }
        
        $message = $this->config['welcome']['message'] ?? 'Bem-vindo!';
        
        return $this->sendSms($to, $message, [
            'user_data' => $userData,
            'is_welcome' => true
        ]);
    }
    
    /**
     * Testar conexão com o provedor
     */
    public function testConnection(): array
    {
        if (!$this->config) {
            throw new Exception('Configuração não encontrada');
        }
        
        $provider = $this->config['provider'];
        
        switch ($provider) {
            case 'twilio':
                return $this->testTwilioConnection();
            case 'nexmo':
                return $this->testNexmoConnection();
            case 'aws_sns':
                return $this->testAwsConnection();
            case 'custom':
                return $this->testCustomConnection();
            default:
                throw new Exception('Provedor não suportado: ' . $provider);
        }
    }
    
    /**
     * Enviar SMS baseado no provedor configurado
     */
    protected function sendByProvider(string $to, string $message, array $options = []): array
    {
        $provider = $this->config['provider'];
        
        switch ($provider) {
            case 'twilio':
                return $this->sendViaTwilio($to, $message, $options);
            case 'nexmo':
                return $this->sendViaNexmo($to, $message, $options);
            case 'aws_sns':
                return $this->sendViaAws($to, $message, $options);
            case 'custom':
                return $this->sendViaCustom($to, $message, $options);
            default:
                throw new Exception('Provedor não configurado: ' . $provider);
        }
    }
    
    /**
     * Enviar via Twilio
     */
    protected function sendViaTwilio(string $to, string $message, array $options = []): array
    {
        $credentials = $this->config['credentials'];
        $settings = $this->config['settings'];
        
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$credentials['account_sid']}/Messages.json";
        
        $data = [
            'From' => $settings['from_number'],
            'To' => $to,
            'Body' => $message
        ];
        
        // Adicionar opções específicas
        if ($options['scheduled_time'] ?? false) {
            $data['SendAt'] = $options['scheduled_time'];
        }
        
        if ($this->config['features']['flash_sms'] ?? false) {
            $data['MessageClass'] = '0';
        }
        
        $headers = [
            'Authorization: Basic ' . base64_encode($credentials['account_sid'] . ':' . $credentials['auth_token']),
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $response = $this->makeHttpRequest($url, 'POST', $data, $headers);
        
        if ($response['status_code'] >= 200 && $response['status_code'] < 300) {
            $responseData = json_decode($response['body'], true);
            return [
                'message_id' => $responseData['sid'],
                'status' => $responseData['status'],
                'provider' => 'twilio',
                'raw_response' => $responseData
            ];
        } else {
            $error = json_decode($response['body'], true);
            throw new Exception('Erro Twilio: ' . ($error['message'] ?? 'Erro desconhecido'));
        }
    }
    
    /**
     * Enviar via Nexmo/Vonage
     */
    protected function sendViaNexmo(string $to, string $message, array $options = []): array
    {
        $credentials = $this->config['credentials'];
        $settings = $this->config['settings'];
        
        $url = 'https://rest.nexmo.com/sms/json';
        
        $data = [
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret'],
            'from' => $settings['from_number'],
            'to' => $to,
            'text' => $message
        ];
        
        // Adicionar opções específicas
        if ($this->config['settings']['unicode_enabled'] ?? false) {
            $data['type'] = 'unicode';
        }
        
        if ($this->config['features']['flash_sms'] ?? false) {
            $data['message-class'] = '0';
        }
        
        $headers = ['Content-Type: application/json'];
        
        $response = $this->makeHttpRequest($url, 'POST', json_encode($data), $headers);
        
        if ($response['status_code'] >= 200 && $response['status_code'] < 300) {
            $responseData = json_decode($response['body'], true);
            $message = $responseData['messages'][0] ?? [];
            
            if ($message['status'] == '0') {
                return [
                    'message_id' => $message['message-id'],
                    'status' => 'sent',
                    'provider' => 'nexmo',
                    'raw_response' => $responseData
                ];
            } else {
                throw new Exception('Erro Nexmo: ' . ($message['error-text'] ?? 'Erro desconhecido'));
            }
        } else {
            throw new Exception('Erro na requisição Nexmo: HTTP ' . $response['status_code']);
        }
    }
    
    /**
     * Enviar via AWS SNS
     */
    protected function sendViaAws(string $to, string $message, array $options = []): array
    {
        $credentials = $this->config['credentials'];
        
        // Implementação simplificada do AWS SNS
        // Em produção, usar o SDK oficial da AWS
        $region = $credentials['region'];
        $url = "https://sns.{$region}.amazonaws.com/";
        
        $data = [
            'Action' => 'Publish',
            'PhoneNumber' => $to,
            'Message' => $message,
            'Version' => '2010-03-31'
        ];
        
        // Gerar assinatura AWS (simplificado)
        $headers = $this->generateAwsHeaders($credentials, $data);
        
        $response = $this->makeHttpRequest($url, 'POST', http_build_query($data), $headers);
        
        if ($response['status_code'] >= 200 && $response['status_code'] < 300) {
            // Parse XML response
            $xml = simplexml_load_string($response['body']);
            $messageId = (string)$xml->PublishResult->MessageId;
            
            return [
                'message_id' => $messageId,
                'status' => 'sent',
                'provider' => 'aws_sns',
                'raw_response' => $response['body']
            ];
        } else {
            throw new Exception('Erro AWS SNS: HTTP ' . $response['status_code']);
        }
    }
    
    /**
     * Enviar via provedor personalizado
     */
    protected function sendViaCustom(string $to, string $message, array $options = []): array
    {
        $credentials = $this->config['credentials'];
        $settings = $this->config['settings'];
        
        $url = $credentials['endpoint'];
        $method = $credentials['method'] ?? 'POST';
        
        $data = [
            'to' => $to,
            'from' => $settings['from_number'],
            'message' => $message
        ];
        
        // Headers personalizados
        $headers = json_decode($credentials['headers'] ?? '{}', true);
        
        // Autenticação
        $authType = $credentials['auth_type'] ?? 'none';
        if ($authType === 'bearer') {
            $headers['Authorization'] = 'Bearer ' . $credentials['auth_token'];
        } elseif ($authType === 'api_key') {
            $headers['X-API-Key'] = $credentials['auth_token'];
        }
        
        // Converter headers para formato cURL
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = $key . ': ' . $value;
        }
        
        $requestData = $method === 'GET' ? null : json_encode($data);
        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
        }
        
        $response = $this->makeHttpRequest($url, $method, $requestData, $curlHeaders);
        
        if ($response['status_code'] >= 200 && $response['status_code'] < 300) {
            $responseData = json_decode($response['body'], true);
            return [
                'message_id' => $responseData['id'] ?? uniqid(),
                'status' => 'sent',
                'provider' => 'custom',
                'raw_response' => $responseData
            ];
        } else {
            throw new Exception('Erro provedor personalizado: HTTP ' . $response['status_code']);
        }
    }
    
    /**
     * Processar mensagem com variáveis
     */
    protected function processMessage(string $message, array $options = []): string
    {
        $variables = [
            '{{app_name}}' => $options['app_name'] ?? 'App',
            '{{user_name}}' => $options['user_name'] ?? 'Usuário',
            '{{message}}' => $options['custom_message'] ?? $message
        ];
        
        // Adicionar variáveis do usuário
        if (isset($options['user_data'])) {
            foreach ($options['user_data'] as $key => $value) {
                $variables['{{' . $key . '}}'] = $value;
            }
        }
        
        $processedMessage = str_replace(array_keys($variables), array_values($variables), $message);
        
        // Verificar limite de caracteres
        $maxLength = $this->config['limits']['max_length'] ?? 160;
        if (strlen($processedMessage) > $maxLength) {
            if ($this->config['features']['concatenated_sms'] ?? false) {
                // Permitir SMS concatenado
                return $processedMessage;
            } else {
                // Truncar mensagem
                return substr($processedMessage, 0, $maxLength - 3) . '...';
            }
        }
        
        return $processedMessage;
    }
    
    /**
     * Validar número de telefone
     */
    protected function validatePhoneNumber(string $phone): string
    {
        // Remover caracteres não numéricos exceto +
        $phone = preg_replace('/[^+\d]/', '', $phone);
        
        // Verificar se começa com +
        if (!str_starts_with($phone, '+')) {
            throw new Exception('Número deve incluir código do país (ex: +5511999999999)');
        }
        
        // Verificar comprimento mínimo
        if (strlen($phone) < 10) {
            throw new Exception('Número de telefone inválido');
        }
        
        return $phone;
    }
    
    /**
     * Verificar limites de envio
     */
    protected function checkSendingLimits(): bool
    {
        $limits = $this->config['limits'] ?? [];
        
        // Verificar limite diário
        if (isset($limits['daily_limit'])) {
            $dailyCount = $this->logModel->where([
                'application_id' => $this->applicationId,
                'channel_type' => 'sms',
                'status' => 'sent',
                'created_at >=' => date('Y-m-d 00:00:00')
            ])->countAllResults();
            
            if ($dailyCount >= $limits['daily_limit']) {
                return false;
            }
        }
        
        // Verificar limite por hora
        if (isset($limits['hourly_limit'])) {
            $hourlyCount = $this->logModel->where([
                'application_id' => $this->applicationId,
                'channel_type' => 'sms',
                'status' => 'sent',
                'created_at >=' => date('Y-m-d H:00:00')
            ])->countAllResults();
            
            if ($hourlyCount >= $limits['hourly_limit']) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Registrar notificação no log
     */
    protected function logNotification(string $recipient, string $message, string $status, array $response = []): void
    {
        $this->logModel->insert([
            'application_id' => $this->applicationId,
            'channel_type' => 'sms',
            'recipient' => $recipient,
            'subject' => null,
            'message' => $message,
            'status' => $status,
            'provider_response' => json_encode($response),
            'sent_at' => $status === 'sent' ? date('Y-m-d H:i:s') : null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Fazer requisição HTTP
     */
    protected function makeHttpRequest(string $url, string $method, $data = null, array $headers = []): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'NotificationSystem/1.0'
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception('Erro cURL: ' . $error);
        }
        
        return [
            'status_code' => $statusCode,
            'body' => $response
        ];
    }
    
    /**
     * Gerar headers AWS (implementação simplificada)
     */
    protected function generateAwsHeaders(array $credentials, array $data): array
    {
        // Implementação simplificada - em produção usar SDK oficial
        return [
            'Content-Type: application/x-www-form-urlencoded',
            'X-Amz-Date: ' . gmdate('Ymd\THis\Z'),
            'Authorization: AWS4-HMAC-SHA256 Credential=' . $credentials['access_key'] . '/...',
        ];
    }
    
    /**
     * Testar conexão Twilio
     */
    protected function testTwilioConnection(): array
    {
        $credentials = $this->config['credentials'];
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$credentials['account_sid']}.json";
        
        $headers = [
            'Authorization: Basic ' . base64_encode($credentials['account_sid'] . ':' . $credentials['auth_token'])
        ];
        
        $response = $this->makeHttpRequest($url, 'GET', null, $headers);
        
        if ($response['status_code'] === 200) {
            return ['success' => true, 'message' => 'Conexão Twilio OK'];
        } else {
            throw new Exception('Falha na conexão Twilio');
        }
    }
    
    /**
     * Testar conexão Nexmo
     */
    protected function testNexmoConnection(): array
    {
        $credentials = $this->config['credentials'];
        $url = 'https://rest.nexmo.com/account/get-balance?' . http_build_query([
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret']
        ]);
        
        $response = $this->makeHttpRequest($url, 'GET');
        
        if ($response['status_code'] === 200) {
            return ['success' => true, 'message' => 'Conexão Nexmo OK'];
        } else {
            throw new Exception('Falha na conexão Nexmo');
        }
    }
    
    /**
     * Testar conexão AWS
     */
    protected function testAwsConnection(): array
    {
        // Implementação simplificada
        return ['success' => true, 'message' => 'Teste AWS SNS (implementação simplificada)'];
    }
    
    /**
     * Testar conexão personalizada
     */
    protected function testCustomConnection(): array
    {
        $credentials = $this->config['credentials'];
        
        try {
            $response = $this->makeHttpRequest($credentials['endpoint'], 'GET');
            return ['success' => true, 'message' => 'Endpoint personalizado acessível'];
        } catch (Exception $e) {
            throw new Exception('Falha na conexão personalizada: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter informações dos provedores
     */
    public static function getProviderInfo(): array
    {
        return [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Plataforma de comunicação em nuvem líder mundial',
                'features' => [
                    'Alta confiabilidade e entregabilidade',
                    'Suporte global com números locais',
                    'APIs robustas e documentação completa',
                    'Relatórios detalhados e analytics',
                    'Suporte a SMS, MMS e WhatsApp'
                ],
                'pricing' => 'A partir de $0.0075 por SMS',
                'setup_url' => 'https://console.twilio.com/'
            ],
            'nexmo' => [
                'name' => 'Nexmo (Vonage)',
                'description' => 'Plataforma de APIs de comunicação da Vonage',
                'features' => [
                    'Cobertura global em 200+ países',
                    'APIs simples e intuitivas',
                    'Suporte a Unicode e emojis',
                    'Relatórios de entrega em tempo real',
                    'Integração com outros serviços Vonage'
                ],
                'pricing' => 'A partir de $0.0045 por SMS',
                'setup_url' => 'https://dashboard.nexmo.com/'
            ],
            'aws_sns' => [
                'name' => 'Amazon SNS',
                'description' => 'Serviço de notificação da Amazon Web Services',
                'features' => [
                    'Integração nativa com AWS',
                    'Escalabilidade automática',
                    'Preços competitivos',
                    'Suporte a múltiplos canais',
                    'Monitoramento com CloudWatch'
                ],
                'pricing' => 'A partir de $0.00645 por SMS',
                'setup_url' => 'https://console.aws.amazon.com/sns/'
            ],
            'custom' => [
                'name' => 'Provedor Personalizado',
                'description' => 'Configure qualquer provedor via API REST',
                'features' => [
                    'Flexibilidade total de configuração',
                    'Suporte a qualquer provedor',
                    'Headers e autenticação personalizados',
                    'Métodos HTTP GET/POST',
                    'Ideal para provedores locais'
                ],
                'pricing' => 'Varia conforme o provedor escolhido',
                'setup_url' => null
            ]
        ];
    }
}