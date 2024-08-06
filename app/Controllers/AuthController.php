<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = $this->userModel->verifyPassword($email, $password);

            if ($user) {
                session()->set([
                    'user_id' => $user['id'],
                    'user_name' => $user['name'],
                    'user_email' => $user['email'],
                    'logged_in' => true
                ]);

                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Email ou senha inválidos.');
                return redirect()->back()->withInput();
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
            ];

            if ($this->userModel->insert($data)) {
                session()->setFlashdata('success', 'Usuário criado com sucesso! Faça login para continuar.');
                return redirect()->to('/login');
            } else {
                session()->setFlashdata('error', 'Erro ao criar usuário.');
                return redirect()->back()->withInput();
            }
        }

        return view('auth/register');
    }
}