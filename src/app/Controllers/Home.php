<?php

namespace App\Controllers;

use App\Controllers\WebController;
use App\Models\DealModel;
use App\Models\LocationModel;

class Home extends WebController
{
    protected $dealModel;
    protected $locationModel;

    public function __construct()
    {
        parent::__construct();
        $this->dealModel = new DealModel();
        $this->locationModel = new LocationModel();
    }

    public function index()
    {
        $deals = $this->getMainDeals();
        
        return $this->render('home/index', [
            'deals' => $deals,
            'title' => 'Página Inicial - E-commerce de Tecnologia'
        ]);
    }
    
    private function getMainDeals()
    {
        $deals = [];
        
        if ($this->user) {
            //TODO: implementar lógica para obter a localização do usuário
            $deals = $this->dealModel->getActiveDeals();
        } else {
            $deals = $this->dealModel->getActiveDeals();
        }

        // Limita a 12 ofertas para a página inicial
        return array_slice($deals, 0, 12);
    }
}
