<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthMvcFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userModel = new UserModel();
        $userId = session()->get('user_id');
        if (!$userId || !$userModel->find($userId)) {
            return redirect()->to('login')->with('fail', 'Acesso negado. Por favor, faça login para continuar.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não é necessário fazer nada após a requisição
    }
}
