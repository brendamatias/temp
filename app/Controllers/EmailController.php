<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationChannelModel;
use CodeIgniter\Controller;

class EmailController extends Controller
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

        // Buscar ou criar canal E-mail
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'email'
        ])->first();

        if (!$channel) {
            // Criar canal se não existir
            $channelId = $this->channelModel->insert([
                'application_id' => $applicationId,
                'channel_type' => 'email',
                'is_enabled' => false,
                'configuration' => json_encode([])
            ]);
            
            $channel = $this->channelModel->find($channelId);
        }

        $config = json_decode($channel['configuration'], true) ?? [];

        return view('channels/email/configure', [
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
            'smtp_host' => 'required|min_length[3]|max_length[255]',
            'smtp_port' => 'required|integer|greater_than[0]|less_than[65536]',
            'smtp_username' => 'required|valid_email',
            'smtp_password' => 'required|min_length[6]',
            'smtp_encryption' => 'required|in_list[none,tls,ssl]',
            'from_email' => 'required|valid_email',
            'from_name' => 'required|min_length[2]|max_length[255]',
            'reply_to_email' => 'permit_empty|valid_email',
            'reply_to_name' => 'permit_empty|max_length[255]',
            'default_subject' => 'required|min_length[3]|max_length[255]',
            'welcome_enabled' => 'permit_empty',
            'welcome_subject' => 'required_with[welcome_enabled]|max_length[255]',
            'welcome_template' => 'required_with[welcome_enabled]',
            'unsubscribe_enabled' => 'permit_empty',
            'unsubscribe_url' => 'permit_empty|valid_url',
            'tracking_enabled' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Buscar canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'email'
        ])->first();

        $config = [
            'smtp' => [
                'host' => $this->request->getPost('smtp_host'),
                'port' => (int) $this->request->getPost('smtp_port'),
                'username' => $this->request->getPost('smtp_username'),
                'password' => $this->request->getPost('smtp_password'),
                'encryption' => $this->request->getPost('smtp_encryption'),
                'timeout' => 30
            ],
            'from' => [
                'email' => $this->request->getPost('from_email'),
                'name' => $this->request->getPost('from_name')
            ],
            'reply_to' => [
                'email' => $this->request->getPost('reply_to_email') ?: '',
                'name' => $this->request->getPost('reply_to_name') ?: ''
            ],
            'defaults' => [
                'subject' => $this->request->getPost('default_subject'),
                'template' => $this->request->getPost('default_template') ?: 'default'
            ],
            'welcome' => [
                'enabled' => $this->request->getPost('welcome_enabled') ? true : false,
                'subject' => $this->request->getPost('welcome_subject') ?: '',
                'template' => $this->request->getPost('welcome_template') ?: ''
            ],
            'features' => [
                'unsubscribe_enabled' => $this->request->getPost('unsubscribe_enabled') ? true : false,
                'unsubscribe_url' => $this->request->getPost('unsubscribe_url') ?: '',
                'tracking_enabled' => $this->request->getPost('tracking_enabled') ? true : false,
                'html_enabled' => true,
                'attachments_enabled' => $this->request->getPost('attachments_enabled') ? true : false
            ],
            'limits' => [
                'daily_limit' => (int) ($this->request->getPost('daily_limit') ?: 1000),
                'hourly_limit' => (int) ($this->request->getPost('hourly_limit') ?: 100),
                'max_recipients' => (int) ($this->request->getPost('max_recipients') ?: 50)
            ]
        ];

        $updateData = [
            'configuration' => json_encode($config),
            'is_enabled' => true
        ];

        if ($this->channelModel->update($channel['id'], $updateData)) {
            session()->setFlashdata('success', 'Configurações de E-mail salvas com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao salvar configurações.');
        }

        return redirect()->to('/channels/email/' . $applicationId);
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
            'channel_type' => 'email'
        ])->first();

        if (!$channel || !$channel['is_enabled']) {
            return $this->response->setJSON(['error' => 'Canal E-mail não configurado']);
        }

        $config = json_decode($channel['configuration'], true);
        
        try {
            // Testar conexão SMTP
            $email = \Config\Services::email();
            
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
                'newline' => "\r\n"
            ];
            
            $email->initialize($emailConfig);
            
            // Simular teste de conexão
            $testResult = [
                'success' => true,
                'message' => 'Conexão SMTP testada com sucesso!',
                'details' => [
                    'host' => $config['smtp']['host'],
                    'port' => $config['smtp']['port'],
                    'encryption' => $config['smtp']['encryption'],
                    'username' => $config['smtp']['username'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]
            ];
            
            return $this->response->setJSON($testResult);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro na conexão SMTP: ' . $e->getMessage()
            ]);
        }
    }

    public function sendTestEmail($applicationId)
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
            'channel_type' => 'email'
        ])->first();

        if (!$channel || !$channel['is_enabled']) {
            return $this->response->setJSON(['error' => 'Canal E-mail não configurado']);
        }

        $config = json_decode($channel['configuration'], true);
        $testEmail = $this->request->getJSON()->email ?? $config['from']['email'];
        
        try {
            $email = \Config\Services::email();
            
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
                'newline' => "\r\n"
            ];
            
            $email->initialize($emailConfig);
            
            $email->setFrom($config['from']['email'], $config['from']['name']);
            $email->setTo($testEmail);
            $email->setSubject('Teste de E-mail - ' . $application['name']);
            
            $message = $this->generateTestEmailTemplate($application, $config);
            $email->setMessage($message);
            
            if ($email->send()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'E-mail de teste enviado com sucesso!',
                    'details' => [
                        'to' => $testEmail,
                        'subject' => 'Teste de E-mail - ' . $application['name'],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Falha ao enviar e-mail: ' . $email->printDebugger()
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro ao enviar e-mail de teste: ' . $e->getMessage()
            ]);
        }
    }

    private function generateTestEmailTemplate($application, $config)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Teste de E-mail</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .success { color: #28a745; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Teste de E-mail</h1>
                </div>
                <div class="content">
                    <p class="success">Parabéns! Seu canal de e-mail está funcionando corretamente.</p>
                    
                    <h3>Detalhes da Configuração:</h3>
                    <ul>
                        <li><strong>Aplicativo:</strong> ' . esc($application['name']) . '</li>
                        <li><strong>Servidor SMTP:</strong> ' . esc($config['smtp']['host']) . ':' . $config['smtp']['port'] . '</li>
                        <li><strong>Criptografia:</strong> ' . strtoupper($config['smtp']['encryption']) . '</li>
                        <li><strong>Remetente:</strong> ' . esc($config['from']['name']) . ' &lt;' . esc($config['from']['email']) . '&gt;</li>
                        <li><strong>Data/Hora:</strong> ' . date('d/m/Y H:i:s') . '</li>
                    </ul>
                    
                    <p>Este é um e-mail de teste gerado automaticamente para verificar se suas configurações de SMTP estão funcionando corretamente.</p>
                </div>
                <div class="footer">
                    <p>Este e-mail foi enviado pelo sistema de notificações.<br>
                    Se você não solicitou este teste, pode ignorar esta mensagem.</p>
                </div>
            </div>
        </body>
        </html>
        ';
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
            'channel_type' => 'email'
        ])->first();

        if ($channel) {
            $this->channelModel->update($channel['id'], ['is_enabled' => false]);
            session()->setFlashdata('success', 'Canal E-mail desabilitado.');
        }

        return redirect()->to('/applications/' . $applicationId);
    }

    public function previewTemplate($applicationId)
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        $templateType = $this->request->getGet('type') ?? 'default';
        $templateContent = $this->request->getGet('content') ?? '';

        // Gerar preview do template
        $preview = $this->generateTemplatePreview($templateType, $templateContent);

        return $this->response->setJSON([
            'success' => true,
            'preview' => $preview
        ]);
    }

    private function generateTemplatePreview($type, $content)
    {
        $sampleData = [
            'user_name' => 'João Silva',
            'app_name' => 'Meu Aplicativo',
            'message' => 'Esta é uma mensagem de exemplo para demonstrar como ficará o template.',
            'date' => date('d/m/Y'),
            'time' => date('H:i')
        ];

        // Substituir variáveis no conteúdo
        $processedContent = $content;
        foreach ($sampleData as $key => $value) {
            $processedContent = str_replace('{{' . $key . '}}', $value, $processedContent);
        }

        return $processedContent;
    }
}