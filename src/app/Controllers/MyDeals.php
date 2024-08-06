<?php

namespace App\Controllers;

use App\Controllers\WebController;
use App\Models\DealModel;
use App\Models\BidModel;
use App\Models\MessageModel;

class MyDeals extends WebController
{
    protected $dealModel;
    protected $bidModel;
    protected $messageModel;

    public function __construct()
    {
        parent::__construct();
        $this->dealModel = new DealModel();
        $this->bidModel = new BidModel();
        $this->messageModel = new MessageModel();
    }
    
    public function index()
    {
        $deals = $this->dealModel->getDealsByUser($this->user['id']);
        
        return $this->render('my-deals/index', [
            'deals' => $deals,
            'title' => 'Minhas Negociações - E-commerce de Tecnologia'
        ]);
    }
    
    public function myBids()
    {
        $bids = $this->bidModel->getBidsByUser($this->user['id']);
        
        $messages = $this->messageModel->getMessagesByUser($this->user['id']);
        
        $dealIds = array_unique(array_merge(
            array_column($bids, 'deal_id'),
            array_column($messages, 'deal_id')
        ));
        
        $deals = [];
        foreach ($dealIds as $dealId) {
            $deal = $this->dealModel->getDealById($dealId);
            if ($deal) {
                $deal['user_bid'] = array_filter($bids, fn($bid) => $bid['deal_id'] == $dealId);
                $deal['user_messages'] = array_filter($messages, fn($msg) => $msg['deal_id'] == $dealId);
                $deals[] = $deal;
            }
        }

        return $this->render('my-deals/my-bids', [
            'deals' => $deals,
            'title' => 'Minhas Ofertas - E-commerce de Tecnologia'
        ]);
    }
}
