<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\NotificationChannelModel;
use CodeIgniter\Controller;

class ApplicationController extends Controller
{
    protected $applicationModel;
    protected $channelModel;

    public function __construct()
    {
        $this->applicationModel = new ApplicationModel();
        $this->channelModel = new NotificationChannelModel();
    }

    public function index()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $applications = $this->applicationModel->where('user_id', session()->get('user_id'))->findAll();
        
        return view('applications/index', [
            'applications' => $applications
        ]);
    }

    public function show($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $application = $this->applicationModel->where([
            'id' => $id,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        $channels = $this->channelModel->where('application_id', $id)->findAll();
        
        return view('applications/show', [
            'application' => $application,
            'channels' => $channels
        ]);
    }

    public function create()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        helper('form');
        return view('applications/create');
    }

    public function store()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[255]',
            'channels' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'user_id' => session()->get('user_id'),
            'name' => $this->request->getPost('name'),
            'is_active' => 1
        ];

        $applicationId = $this->applicationModel->insert($data);

        if ($applicationId) {
            // Criar canais selecionados
            $channels = $this->request->getPost('channels');
            foreach ($channels as $channel) {
                $this->channelModel->insert([
                    'application_id' => $applicationId,
                    'channel_type' => $channel,
                    'is_enabled' => false,
                    'configuration' => json_encode([])
                ]);
            }

            session()->setFlashdata('success', 'Aplicativo criado com sucesso!');
            return redirect()->to('/applications/' . $applicationId);
        } else {
            session()->setFlashdata('error', 'Erro ao criar aplicativo.');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $application = $this->applicationModel->where([
            'id' => $id,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        $channels = $this->channelModel->where('application_id', $id)->findAll();
        
        helper('form');
        return view('applications/edit', [
            'application' => $application,
            'channels' => $channels
        ]);
    }

    public function update($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $application = $this->applicationModel->where([
            'id' => $id,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[255]',
            'is_active' => 'required|in_list[0,1]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'is_active' => $this->request->getPost('is_active')
        ];

        if ($this->applicationModel->update($id, $data)) {
            session()->setFlashdata('success', 'Aplicativo atualizado com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao atualizar aplicativo.');
        }

        return redirect()->to('/applications/' . $id);
    }

    public function delete($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $application = $this->applicationModel->where([
            'id' => $id,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        if ($this->applicationModel->delete($id)) {
            session()->setFlashdata('success', 'Aplicativo excluído com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao excluir aplicativo.');
        }

        return redirect()->to('/dashboard');
    }

    public function regenerateKeys($id)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $application = $this->applicationModel->where([
            'id' => $id,
            'user_id' => session()->get('user_id')
        ])->first();

        if (!$application) {
            session()->setFlashdata('error', 'Aplicativo não encontrado.');
            return redirect()->to('/dashboard');
        }

        $data = [
            'api_key' => bin2hex(random_bytes(16)),
            'api_secret' => bin2hex(random_bytes(32))
        ];

        if ($this->applicationModel->update($id, $data)) {
            session()->setFlashdata('success', 'Chaves API regeneradas com sucesso!');
        } else {
            session()->setFlashdata('error', 'Erro ao regenerar chaves API.');
        }

        return redirect()->to('/applications/' . $id);
    }
}