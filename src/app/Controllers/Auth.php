<?php

namespace App\Controllers;

use App\Controllers\WebController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends WebController
{
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    
    public function login()
    {
        if ($this->user) {
            return redirect()->to('/');
        }

        return $this->render('auth/login', [
            'title' => 'Login - E-commerce de Tecnologia'
        ]);
    }
    
    public function doLogin()
    {
        $login = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        
        if (empty($login) || empty($password)) {
            return $this->redirectWithError('/login', 'Login e senha são obrigatórios');
        }
        
        $user = $this->userModel->getUserByLogin($login);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->redirectWithError('/login', 'Login ou senha inválidos');
        }
        
        $this->session->set([
            'user_id' => $user['id'],
            'user_login' => $user['login'],
            'user_name' => $user['name'],
            'user_email' => $user['email']
        ]);

        return $this->redirectWithSuccess('/', 'Login realizado com sucesso!');
    }
    
    public function sso()
    {
        $token = $this->request->getGet('token');
        
        if (empty($token)) {
            return $this->redirectWithError('/login', 'Token SSO não fornecido');
        }

        try {
            $key = getenv('JWT_SECRET') ?: 'default_secret_key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            $user = $this->userModel->find($decoded->user_id);
            
            if (!$user) {
                return $this->redirectWithError('/login', 'Usuário não encontrado');
            }
            
            $this->session->set([
                'user_id' => $user['id'],
                'user_login' => $user['login'],
                'user_name' => $user['name'],
                'user_email' => $user['email']
            ]);

            return $this->redirectWithSuccess('/', 'Login SSO realizado com sucesso!');

        } catch (\Exception $e) {
            return $this->redirectWithError('/login', 'Token SSO inválido ou expirado');
        }
    }
    
    public function logout()
    {
        $this->session->destroy();
        return $this->redirectWithInfo('/', 'Logout realizado com sucesso');
    }
}
