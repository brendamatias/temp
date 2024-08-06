<?php

namespace App\Controllers;

use App\Controllers\WebController;
use App\Models\DealModel;
use App\Models\BidModel;
use App\Models\MessageModel;
use App\Models\LocationModel;

class Deals extends WebController
{
    protected $dealModel;
    protected $bidModel;
    protected $messageModel;
    protected $locationModel;

    public function __construct()
    {
        parent::__construct();
        $this->dealModel = new DealModel();
        $this->bidModel = new BidModel();
        $this->messageModel = new MessageModel();
        $this->locationModel = new LocationModel();
    }
    
    public function index()
    {
        $deals = $this->dealModel->getActiveDeals();
        
        return $this->render('deals/index', [
            'deals' => $deals,
            'title' => 'Negociações - E-commerce de Tecnologia'
        ]);
    }

    /**
     * API endpoint para buscar deals com filtros
     */
    public function search()
    {
        $filters = $this->request->getGet();
        
        try {
            $deals = $this->dealModel->searchDeals($filters);
            
            // Processar deals para incluir fotos
            foreach ($deals as &$deal) {
                $deal['photos'] = $this->getDealPhotos($deal['id']);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $deals,
                'count' => count($deals)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar negociações: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Obter fotos de um deal
     */
    private function getDealPhotos($dealId)
    {
        // TODO: Implementar busca de fotos quando o modelo estiver disponível
        return [];
    }
    
    public function create()
    {
        return $this->render('deals/create', [
            'title' => 'Criar Negociação - E-commerce de Tecnologia'
        ]);
    }
    
    public function store()
    {
        $data = $this->request->getPost();
        
        if (empty($data['description']) || empty($data['value'])) {
            return $this->redirectWithError('/deals/create', 'Descrição e valor são obrigatórios');
        }
        
        $dealData = [
            'user_id' => $this->user['id'],
            'type' => $data['type'] ?? 1, // 1 = venda, 2 = troca
            'value' => $data['value'],
            'description' => $data['description'],
            'trade_for' => $data['trade_for'] ?? null,
            'urgency_type' => $data['urgency_type'] ?? null,
            'urgency_limit_date' => $data['urgency_limit_date'] ?? null,
            'status' => 'active'
        ];
        
        $locationData = [
            'lat' => $data['lat'] ?? 0,
            'lng' => $data['lng'] ?? 0,
            'address' => $data['address'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zip_code' => $data['zip_code'] ?? ''
        ];

        try {
            $location = $this->locationModel->createLocation($locationData);
            if (!$location) {
                $errors = $this->locationModel->errors();
                $errorMessage = 'Erro ao criar localização: ' . implode(', ', $errors);
                throw new \Exception($errorMessage);
            }
            
            $deal = $this->dealModel->createDeal($dealData, $location['id']);
            if (!$deal) {
                $errors = $this->dealModel->errors();
                $errorMessage = 'Erro ao criar negociação: ' . implode(', ', $errors);
                throw new \Exception($errorMessage);
            }

            return $this->redirectWithSuccess("/deals/{$deal['id']}", 'Negociação criada com sucesso!');

        } catch (\Exception $e) {
            return $this->redirectWithError('/deals/create', 'Erro ao criar negociação: ' . $e->getMessage());
        }
    }
    
    public function show($id = null)
    {
        if (!$id) {
            return redirect()->to('/deals');
        }

        $deal = $this->dealModel->getDealById($id);
        if (!$deal) {
            return $this->redirectWithError('/deals', 'Negociação não encontrada');
        }
        
        $bids = $this->bidModel->getBidsByDeal($id);
        $messages = $this->messageModel->getMessagesByDeal($id);
        
        $isOwner = $this->user && $this->user['id'] == $deal['user_id'];
        
        $userBid = null;
        if ($this->user && !$isOwner) {
            $userBid = $this->bidModel->getUserBidForDeal($this->user['id'], $id);
        }

        return $this->render('deals/show', [
            'deal' => $deal,
            'bids' => $bids,
            'messages' => $messages,
            'isOwner' => $isOwner,
            'isAuthenticated' => $this->user !== null,
            'userBid' => $userBid,
            'title' => "Negociação - {$deal['description']}"
        ]);
    }
    
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('/deals');
        }

        $deal = $this->dealModel->getDealById($id);
        if (!$deal) {
            return $this->redirectWithError('/deals', 'Negociação não encontrada');
        }
        
        if ($this->user['id'] != $deal['user_id']) {
            return $this->redirectWithError('/deals', 'Você não tem permissão para editar esta negociação');
        }

        return $this->render('deals/edit', [
            'deal' => $deal,
            'title' => 'Editar Negociação - E-commerce de Tecnologia'
        ]);
    }
    
    public function update($id = null)
    {
        if (!$id) {
            return redirect()->to('/deals');
        }

        $deal = $this->dealModel->getDealById($id);
        if (!$deal) {
            return $this->redirectWithError('/deals', 'Negociação não encontrada');
        }
        
        if ($this->user['id'] != $deal['user_id']) {
            return $this->redirectWithError('/deals', 'Você não tem permissão para editar esta negociação');
        }

        $data = $this->request->getPost();
        
        if (empty($data['description']) || empty($data['value'])) {
            return $this->redirectWithError("/deals/{$id}/edit", 'Descrição e valor são obrigatórios');
        }

        try {
            $updated = $this->dealModel->updateDeal($id, $data);
            
            if ($updated) {
                return $this->redirectWithSuccess("/deals/{$id}", 'Negociação atualizada com sucesso!');
            } else {
                throw new \Exception('Erro ao atualizar negociação');
            }

        } catch (\Exception $e) {
            return $this->redirectWithError("/deals/{$id}/edit", 'Erro ao atualizar negociação: ' . $e->getMessage());
        }
    }
}
