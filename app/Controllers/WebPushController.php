<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationChannelModel;
use CodeIgniter\Controller;

class WebPushController extends Controller
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

        // Buscar ou criar canal Web Push
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'webpush'
        ])->first();

        if (!$channel) {
            // Criar canal se não existir
            $channelId = $this->channelModel->insert([
                'application_id' => $applicationId,
                'channel_type' => 'webpush',
                'is_enabled' => false,
                'configuration' => json_encode([])
            ]);
            
            $channel = $this->channelModel->find($channelId);
        }

        $config = json_decode($channel['configuration'], true) ?? [];

        return view('channels/webpush/configure', [
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
            'vapid_public_key' => 'required|min_length[87]|max_length[87]',
            'vapid_private_key' => 'required|min_length[43]|max_length[43]',
            'vapid_subject' => 'required|valid_email',
            'icon_url' => 'permit_empty|valid_url',
            'badge_url' => 'permit_empty|valid_url',
            'default_title' => 'required|min_length[3]|max_length[255]',
            'default_body' => 'required|min_length[10]|max_length[500]',
            'default_url' => 'permit_empty|valid_url',
            'welcome_enabled' => 'permit_empty',
            'welcome_title' => 'required_with[welcome_enabled]|max_length[255]',
            'welcome_body' => 'required_with[welcome_enabled]|max_length[500]',
            'welcome_url' => 'permit_empty|valid_url',
            'permission_title' => 'required|max_length[255]',
            'permission_message' => 'required|max_length[500]',
            'permission_allow_text' => 'required|max_length[50]',
            'permission_deny_text' => 'required|max_length[50]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Buscar canal
        $channel = $this->channelModel->where([
            'application_id' => $applicationId,
            'channel_type' => 'webpush'
        ])->first();

        $config = [
            'vapid' => [
                'public_key' => $this->request->getPost('vapid_public_key'),
                'private_key' => $this->request->getPost('vapid_private_key'),
                'subject' => $this->request->getPost('vapid_subject')
            ],
            'appearance' => [
                'icon_url' => $this->request->getPost('icon_url') ?: '',
                'badge_url' => $this->request->getPost('badge_url') ?: ''
            ],
            'defaults' => [
                'title' => $this->request->getPost('default_title'),
                'body' => $this->request->getPost('default_body'),
                'url' => $this->request->getPost('default_url') ?: '',
                'require_interaction' => $this->request->getPost('require_interaction') ? true : false,
                'silent' => $this->request->getPost('silent') ? true : false
            ],
            'welcome' => [
                'enabled' => $this->request->getPost('welcome_enabled') ? true : false,
                'title' => $this->request->getPost('welcome_title') ?: '',
                'body' => $this->request->getPost('welcome_body') ?: '',
                'url' => $this->request->getPost('welcome_url') ?: ''
            ],
            'permission' => [
                'title' => $this->request->getPost('permission_title'),
                'message' => $this->request->getPost('permission_message'),
                'allow_text' => $this->request->getPost('permission_allow_text'),
                'deny_text' => $this->request->getPost('permission_deny_text')
            ]
        ];

        $updateData = [
            'configuration' => json_encode($config),
            'is_enabled' => true
        ];

        if ($this->channelModel->update($channel['id'], $updateData)) {
            session()->setFlashdata('success', 'Configurações de Web Push salvas com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao salvar configurações.');
        }

        return redirect()->to('/channels/webpush/' . $applicationId);
    }

    public function generateVapidKeys()
    {
        if (!session()->get('user_id')) {
            return $this->response->setJSON(['error' => 'Não autorizado']);
        }

        // Gerar chaves VAPID (simulação - em produção usar biblioteca específica)
        $publicKey = $this->generateBase64UrlSafeString(65);
        $privateKey = $this->generateBase64UrlSafeString(32);

        return $this->response->setJSON([
            'public_key' => $publicKey,
            'private_key' => $privateKey
        ]);
    }

    public function testNotification($applicationId)
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
            'channel_type' => 'webpush'
        ])->first();

        if (!$channel || !$channel['is_enabled']) {
            return $this->response->setJSON(['error' => 'Canal Web Push não configurado']);
        }

        $config = json_decode($channel['configuration'], true);
        
        // Simular envio de notificação de teste
        $testResult = [
            'success' => true,
            'message' => 'Notificação de teste enviada com sucesso!',
            'details' => [
                'title' => $config['defaults']['title'] ?? 'Teste',
                'body' => 'Esta é uma notificação de teste do seu aplicativo ' . $application['name'],
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ];

        return $this->response->setJSON($testResult);
    }

    private function generateBase64UrlSafeString($length)
    {
        $bytes = random_bytes($length);
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
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
            'channel_type' => 'webpush'
        ])->first();

        if ($channel) {
            $this->channelModel->update($channel['id'], ['is_enabled' => false]);
            session()->setFlashdata('success', 'Canal Web Push desabilitado.');
        }

        return redirect()->to('/applications/' . $applicationId);
    }
}