<?php

namespace App\Controllers;

use App\Controllers\WebController;
use App\Models\InviteModel;

class Invites extends WebController
{
    protected $inviteModel;

    public function __construct()
    {
        parent::__construct();
        $this->inviteModel = new InviteModel();
    }
    
    public function index()
    {
        $invites = $this->inviteModel->getInvitesByUser($this->user['id']);
        
        return $this->render('invites/index', [
            'invites' => $invites,
            'title' => 'Meus Convites - E-commerce de Tecnologia'
        ]);
    }
    
    public function create()
    {
        return $this->render('invites/create', [
            'title' => 'Criar Convite - E-commerce de Tecnologia'
        ]);
    }
    
    public function store()
    {
        $data = $this->request->getPost();
        
        if (empty($data['email']) || empty($data['name'])) {
            return $this->redirectWithError('/invites/create', 'Email e nome são obrigatórios');
        }
        
        $existingInvite = $this->inviteModel->getInviteByEmail($data['email']);
        if ($existingInvite) {
            return $this->redirectWithError('/invites/create', 'Este email já foi convidado');
        }
        
        $inviteData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'user' => $this->user['id'],
            'message' => $data['message'] ?? '',
            'expires_at' => $data['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+7 days')),
            'max_uses' => $data['max_uses'] ?? 1
        ];

        try {
            $invite = $this->inviteModel->createWebInvite($inviteData);
            
            if ($invite) {
                $this->sendInviteEmail($invite);
                
                return $this->redirectWithSuccess('/invites', 'Convite enviado com sucesso!');
            } else {
                throw new \Exception('Erro ao criar convite');
            }

        } catch (\Exception $e) {
            return $this->redirectWithError('/invites/create', 'Erro ao criar convite: ' . $e->getMessage());
        }
    }
    
    private function sendInviteEmail($invite)
    {
        // TODO: Implementar envio de email
    }

    /**
     * API endpoint para ver detalhes do convite
     */
    public function details($id)
    {
        // Check if user is authenticated
        if (!$this->user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuário não autenticado'
            ])->setStatusCode(401);
        }

        $invite = $this->inviteModel->getInviteById($id, $this->user['id']);
        
        if (!$invite) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convite não encontrado'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $invite
        ]);
    }

    /**
     * API endpoint para reenviar convite
     */
    public function resend($id)
    {
        $invite = $this->inviteModel->getInviteById($id, $this->user['id']);
        
        if (!$invite) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convite não encontrado'
            ])->setStatusCode(404);
        }

        if ($invite['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas convites pendentes podem ser reenviados'
            ])->setStatusCode(400);
        }

        try {
            // Atualizar data de expiração para 7 dias a partir de agora
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            
            $this->inviteModel->update($id, [
                'expires_at' => $expiresAt,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // TODO: Implementar envio de email
            $this->sendInviteEmail($invite);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convite reenviado com sucesso',
                'data' => [
                    'expires_at' => $expiresAt
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao reenviar convite: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * API endpoint para cancelar convite
     */
    public function cancel($id)
    {
        $invite = $this->inviteModel->getInviteById($id, $this->user['id']);
        
        if (!$invite) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convite não encontrado'
            ])->setStatusCode(404);
        }

        if ($invite['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas convites pendentes podem ser cancelados'
            ])->setStatusCode(400);
        }

        try {
            // Atualizar status para rejeitado (cancelado)
            $this->inviteModel->update($id, [
                'status' => 'rejected',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Convite cancelado com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao cancelar convite: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
