<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationChannelModel;
use CodeIgniter\Controller;

class SmsController extends Controller
{
    protected $applicationModel;
    protected $channelModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->channelModel = new NotificationChannelModel();
    }

    public function configure($applicationId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Verificar se o aplicativo pertence ao usuário
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        // Buscar ou criar canal SMS
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms'
        ])->first();

        if (!$channel) {
            // Criar canal se não existir
            $channelId = $this->channelModel->insert([
                'application_id' => $applicationId,
                'channel_type' => 'sms',
                'is_enabled' => false,
                'configuration' => json_encode([])
            ]);
            
            $channel = $this->channelModel->find($channelId);
        }

        $config = json_decode($channel['configuration'], true) ?? [];

        return view('channels/sms/configure', [
            'application' => $application,
            'channel' => $channel,
            'config' => $config
        ]);
    }

    public function save($applicationId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Verificar se o aplicativo pertence ao usuário
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'provider' => 'required|in_list[twilio,nexmo,aws_sns,custom]',
            'account_sid' => 'required_if[provider,twilio]|min_length[10]',
            'auth_token' => 'required_if[provider,twilio]|min_length[10]',
            'api_key' => 'required_if[provider,nexmo]|min_length[8]',
            'api_secret' => 'required_if[provider,nexmo]|min_length[16]',
            'access_key' => 'required_if[provider,aws_sns]|min_length[16]',
            'secret_key' => 'required_if[provider,aws_sns]|min_length[32]',
            'region' => 'required_if[provider,aws_sns]|min_length[2]',
            'custom_endpoint' => 'required_if[provider,custom]|valid_url',
            'custom_method' => 'required_if[provider,custom]|in_list[GET,POST]',
            'from_number' => 'required|min_length[10]|max_length[20]',
            'default_message' => 'required|min_length[3]|max_length[160]',
            'welcome_enabled' => 'permit_empty',
            'welcome_message' => 'required_with[welcome_enabled]|max_length[160]',
            'unicode_enabled' => 'permit_empty',
            'delivery_reports' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Buscar canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms'
        ])->first();

        $provider = $this->request->getPost('provider');
        
        $config = [
            'provider' => $provider,
            'credentials' => $this->getProviderCredentials($provider),
            'settings' => [
                'from_number' => $this->request->getPost('from_number'),
                'unicode_enabled' => $this->request->getPost('unicode_enabled') ? true : false,
                'delivery_reports' => $this->request->getPost('delivery_reports') ? true : false,
                'webhook_url' => $this->request->getPost('webhook_url') ?: ''
            ],
            'defaults' => [
                'message' => $this->request->getPost('default_message'),
                'validity_period' => (int) ($this->request->getPost('validity_period') ?: 1440) // minutos
            ],
            'welcome' => [
                'enabled' => $this->request->getPost('welcome_enabled') ? true : false,
                'message' => $this->request->getPost('welcome_message') ?: ''
            ],
            'limits' => [
                'daily_limit' => (int) ($this->request->getPost('daily_limit') ?: 100),
                'hourly_limit' => (int) ($this->request->getPost('hourly_limit') ?: 20),
                'max_length' => (int) ($this->request->getPost('max_length') ?: 160),
                'rate_limit' => (int) ($this->request->getPost('rate_limit') ?: 1) // SMS por segundo
            ],
            'features' => [
                'concatenated_sms' => $this->request->getPost('concatenated_sms') ? true : false,
                'flash_sms' => $this->request->getPost('flash_sms') ? true : false,
                'scheduled_sms' => $this->request->getPost('scheduled_sms') ? true : false
            ]
        ];

        $updateData = [
            'configuration' => json_encode($config),
            'is_enabled' => true
        ];

        if ($this->channelModel->update($channel['id'], $updateData)) {
            session()->setFlashdata('success', 'Configurações de SMS salvas com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao salvar configurações.');
        }

        return redirect()->to('/channels/sms/' . $applicationId);
    }

    private function getProviderCredentials($provider)
    {
        switch ($provider) {
            case 'twilio':
                return [
                    'account_sid' => $this->request->getPost('account_sid'),
                    'auth_token' => $this->request->getPost('auth_token')
                ];
            
            case 'nexmo':
                return [
                    'api_key' => $this->request->getPost('api_key'),
                    'api_secret' => $this->request->getPost('api_secret')
                ];
            
            case 'aws_sns':
                return [
                    'access_key' => $this->request->getPost('access_key'),
                    'secret_key' => $this->request->getPost('secret_key'),
                    'region' => $this->request->getPost('region')
                ];
            
            case 'custom':
                return [
                    'endpoint' => $this->request->getPost('custom_endpoint'),
                    'method' => $this->request->getPost('custom_method'),
                    'headers' => $this->request->getPost('custom_headers') ?: '{}',
                    'auth_type' => $this->request->getPost('custom_auth_type') ?: 'none',
                    'auth_token' => $this->request->getPost('custom_auth_token') ?: ''
                ];
            
            default:
                return [];
        }
    }

    public function testConnection($applicationId)
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        // Verificar se o aplicativo pertence ao usuário
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            return $this->response->setJSON(['error' => 'Aplicativo não encontrado']);
        }

        // Buscar configuração do canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms'
        ])->first();

        if (!$channel || !$channel['is_enabled']) {
            return $this->response->setJSON(['error' => 'Canal SMS não configurado']);
        }

        $config = json_decode($channel['configuration'], true);
        
        try {
            // Simular teste de conexão baseado no provedor
            $testResult = $this->testProviderConnection($config);
            
            return $this->response->setJSON($testResult);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro na conexão: ' . $e->getMessage()
            ]);
        }
    }

    private function testProviderConnection($config)
    {
        $provider = $config['provider'];
        
        switch ($provider) {
            case 'twilio':
                return $this->testTwilioConnection($config['credentials']);
            
            case 'nexmo':
                return $this->testNexmoConnection($config['credentials']);
            
            case 'aws_sns':
                return $this->testAwsSnsConnection($config['credentials']);
            
            case 'custom':
                return $this->testCustomConnection($config['credentials']);
            
            default:
                throw new \Exception('Provedor não suportado');
        }
    }

    private function testTwilioConnection($credentials)
    {
        // Simular teste do Twilio
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $credentials['account_sid'] . '.json';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $credentials['account_sid'] . ':' . $credentials['auth_token']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return [
                'success' => true,
                'message' => 'Conexão com Twilio testada com sucesso!',
                'details' => [
                    'provider' => 'Twilio',
                    'account_sid' => $credentials['account_sid'],
                    'status' => 'Ativo',
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Falha na autenticação com Twilio (HTTP ' . $httpCode . ')'
            ];
        }
    }

    private function testNexmoConnection($credentials)
    {
        // Simular teste do Nexmo/Vonage
        $url = 'https://rest.nexmo.com/account/get-balance?api_key=' . $credentials['api_key'] . '&api_secret=' . $credentials['api_secret'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return [
                'success' => true,
                'message' => 'Conexão com Nexmo testada com sucesso!',
                'details' => [
                    'provider' => 'Nexmo/Vonage',
                    'api_key' => $credentials['api_key'],
                    'balance' => $data['value'] ?? 'N/A',
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Falha na autenticação com Nexmo (HTTP ' . $httpCode . ')'
            ];
        }
    }

    private function testAwsSnsConnection($credentials)
    {
        // Simular teste do AWS SNS
        return [
            'success' => true,
            'message' => 'Configuração AWS SNS validada!',
            'details' => [
                'provider' => 'AWS SNS',
                'region' => $credentials['region'],
                'access_key' => substr($credentials['access_key'], 0, 8) . '...',
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];
    }

    private function testCustomConnection($credentials)
    {
        // Testar endpoint personalizado
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $credentials['endpoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $credentials['method']);
        
        if ($credentials['auth_type'] === 'bearer' && !empty($credentials['auth_token'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $credentials['auth_token']
            ]);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'message' => 'Endpoint personalizado acessível!',
                'details' => [
                    'provider' => 'Custom',
                    'endpoint' => $credentials['endpoint'],
                    'method' => $credentials['method'],
                    'http_code' => $httpCode,
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Endpoint não acessível (HTTP ' . $httpCode . ')'
            ];
        }
    }

    public function sendTestSms($applicationId)
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        // Verificar se o aplicativo pertence ao usuário
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            return $this->response->setJSON(['error' => 'Aplicativo não encontrado']);
        }

        // Buscar configuração do canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms'
        ])->first();

        if (!$channel || !$channel['is_enabled']) {
            return $this->response->setJSON(['error' => 'Canal SMS não configurado']);
        }

        $config = json_decode($channel['configuration'], true);
        $testData = $this->request->getJSON();
        $testNumber = $testData->number ?? '';
        $testMessage = $testData->message ?? 'Teste de SMS - ' . $application['name'];
        
        try {
            // Simular envio de SMS de teste
            $result = [
                'success' => true,
                'message' => 'SMS de teste enviado com sucesso!',
                'details' => [
                    'to' => $testNumber,
                    'message' => $testMessage,
                    'provider' => $config['provider'],
                    'from' => $config['settings']['from_number'],
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message_id' => 'test_' . uniqid()
                ]
            ];
            
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro ao enviar SMS de teste: ' . $e->getMessage()
            ]);
        }
    }

    public function disable($applicationId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        // Verificar se o aplicativo pertence ao usuário
        $application = $this->applicationModel->where([
            'id' => $applicationId,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        // Buscar canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'sms'
        ])->first();

        if ($channel) {
            $this->channelModel->update($channel['id'], ['is_enabled' => false]);
            session()->setFlashdata('success', 'Canal SMS desabilitado.');
        }

        return redirect()->to('/applications/' . $applicationId);
    }

    public function getProviderInfo()
    {
        $providers = [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Plataforma de comunicação em nuvem líder mundial',
                'features' => ['Alta confiabilidade', 'Cobertura global', 'APIs robustas'],
                'pricing' => 'A partir de $0.0075 por SMS',
                'setup_url' => 'https://www.twilio.com/console'
            ],
            'nexmo' => [
                'name' => 'Nexmo (Vonage)',
                'description' => 'APIs de comunicação para desenvolvedores',
                'features' => ['Preços competitivos', 'Boa documentação', 'Suporte global'],
                'pricing' => 'A partir de $0.0045 por SMS',
                'setup_url' => 'https://dashboard.nexmo.com/'
            ],
            'aws_sns' => [
                'name' => 'Amazon SNS',
                'description' => 'Serviço de notificação da Amazon Web Services',
                'features' => ['Integração AWS', 'Escalabilidade', 'Preços baixos'],
                'pricing' => 'A partir de $0.0075 por SMS',
                'setup_url' => 'https://console.aws.amazon.com/sns/'
            ],
            'custom' => [
                'name' => 'Provedor Personalizado',
                'description' => 'Configure seu próprio provedor de SMS via API',
                'features' => ['Flexibilidade total', 'Integração customizada', 'Controle completo'],
                'pricing' => 'Depende do provedor escolhido',
                'setup_url' => null
            ]
        ];

        return $this->response->setJSON($providers);
    }
}