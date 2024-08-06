<?php

namespace App\Controllers;

use App\Models\MessageModel;
use App\Models\DealModel;
use App\Models\UserModel;

class Messages extends WebController
{
    protected $messageModel;
    protected $dealModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->messageModel = new MessageModel();
        $this->dealModel = new DealModel();
        $this->userModel = new UserModel();
    }

    public function create($dealId)
    {
        $deal = $this->dealModel->getDealById($dealId);
        if (!$deal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Negociação não encontrada.'
            ]);
        }
        
        if ($deal['user_id'] == $this->user['id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não pode enviar mensagem para sua própria negociação.'
            ]);
        }
        
        if ($this->request->getMethod() === 'POST') {
            $messageData = [
                'deal_id' => $dealId,
                'user_id' => $this->user['id'],
                'title' => 'Mensagem para ' . $deal['description'],
                'message' => $this->request->getPost('message')
            ];
            
            if (empty($messageData['message'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'A mensagem não pode estar vazia.'
                ]);
            }
            
            $message = $this->messageModel->createMessage($messageData);
            
            if ($message) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mensagem enviada com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao enviar mensagem. Tente novamente.'
                ]);
            }
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
    }

    public function delete($dealId, $messageId)
    {
        $message = $this->messageModel->getMessageById($messageId, $dealId);
        if (!$message) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mensagem não encontrada.'
            ]);
        }

        if ($message['user_id'] != $this->user['id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não pode excluir esta mensagem.'
            ]);
        }
        
        if ($this->messageModel->delete($messageId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mensagem excluída com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao excluir mensagem. Tente novamente.'
            ]);
        }
    }
}
