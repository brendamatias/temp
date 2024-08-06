<?php

namespace App\Controllers;

use App\Models\BidModel;
use App\Models\DealModel;
use App\Models\UserModel;

class Bids extends WebController
{
    protected $bidModel;
    protected $dealModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->bidModel = new BidModel();
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
                'message' => 'Você não pode fazer oferta para sua própria negociação.'
            ]);
        }
        
        $existingBid = $this->bidModel->getUserBidForDeal($this->user['id'], $dealId);
        if ($existingBid) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você já fez uma oferta para esta negociação.'
            ]);
        }
        
        if ($this->request->getMethod() === 'POST') {
            $type = $this->request->getPost('type');
            $value = $this->request->getPost('value');
            $tradeFor = $this->request->getPost('trade_for');
            $description = $this->request->getPost('description');
            
            if (empty($type)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Selecione o tipo de oferta.'
                ]);
            }
            
            $bidData = [
                'deal_id' => $dealId,
                'user_id' => $this->user['id'],
                'type' => $type,
                'accepted' => null,
                'description' => $description
            ];

            if ($type == 1) { // Compra
                if (empty($value) || $value <= 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Informe um valor válido para a oferta.'
                    ]);
                }
                $bidData['value'] = $value;
                $bidData['trade_for'] = null;
            } elseif ($type == 2) { // Troca
                if (empty($tradeFor)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Descreva o que você oferece em troca.'
                    ]);
                }
                $bidData['value'] = null;
                $bidData['trade_for'] = $tradeFor;
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de oferta inválido.'
                ]);
            }
            
            $bid = $this->bidModel->createBid($bidData);
            
            if ($bid) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Oferta enviada com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao enviar oferta. Tente novamente.'
                ]);
            }
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
    }

    public function accept($dealId, $bidId)
    {
        $deal = $this->dealModel->getDealById($dealId);
        if (!$deal || $deal['user_id'] != $this->user['id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Negociação não encontrada ou você não tem permissão.'
            ]);
        }
        
        $bid = $this->bidModel->getBidById($bidId, $dealId);
        if (!$bid) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Oferta não encontrada.'
            ]);
        }
        
        if ($this->bidModel->updateBid($bidId, $dealId, ['accepted' => 1])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Oferta aceita com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao aceitar oferta. Tente novamente.'
            ]);
        }
    }

    public function reject($dealId, $bidId)
    {
        $deal = $this->dealModel->getDealById($dealId);
        if (!$deal || $deal['user_id'] != $this->user['id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Negociação não encontrada ou você não tem permissão.'
            ]);
        }
        
        $bid = $this->bidModel->getBidById($bidId, $dealId);
        if (!$bid) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Oferta não encontrada.'
            ]);
        }
        
        if ($this->bidModel->updateBid($bidId, $dealId, ['accepted' => 0])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Oferta rejeitada.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao rejeitar oferta. Tente novamente.'
            ]);
        }
    }

    public function cancel($dealId, $bidId)
    {
        $bid = $this->bidModel->getBidById($bidId, $dealId);
        if (!$bid || $bid['user_id'] != $this->user['id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Oferta não encontrada ou você não tem permissão.'
            ]);
        }
        
        if ($this->bidModel->delete($bidId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Oferta cancelada com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao cancelar oferta. Tente novamente.'
            ]);
        }
    }
}
