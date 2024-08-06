<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

abstract class WebController extends BaseController
{
    protected $session;
    protected $user;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->user = $this->getCurrentUser();
    }

    /**
     * Verifica se o usuário está autenticado
     */
    protected function requireAuth()
    {
        if (!$this->user) {
            return redirect()->to('/login');
        }
        return null;
    }

    /**
     * Obtém o usuário atual da sessão
     */
    protected function getCurrentUser()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->find($userId);
    }

    /**
     * Renderiza uma view com dados comuns
     */
    protected function render($view, $data = [])
    {
        // Dados comuns para todas as views
        $commonData = [
            'user' => $this->user,
            'isAuthenticated' => !empty($this->user),
            'currentUrl' => current_url(),
            'baseUrl' => base_url(),
        ];

        $data = array_merge($commonData, $data);
        
        return view($view, $data);
    }

    /**
     * Redireciona com mensagem de sucesso
     */
    protected function redirectWithSuccess($url, $message)
    {
        $this->session->setFlashdata('success', $message);
        return redirect()->to($url);
    }

    /**
     * Redireciona com mensagem de erro
     */
    protected function redirectWithError($url, $message)
    {
        $this->session->setFlashdata('error', $message);
        return redirect()->to($url);
    }

    /**
     * Redireciona com mensagem de informação
     */
    protected function redirectWithInfo($url, $message)
    {
        $this->session->setFlashdata('info', $message);
        return redirect()->to($url);
    }
}
