<?php

namespace App\Models;

use CodeIgniter\Model;

class BidModel extends Model
{
    protected $table = 'bids';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'deal_id',
        'user_id',
        'accepted',
        'type',
        'value',
        'trade_for',
        'description'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function createBid(array $bidData)
    {
        // Filtra apenas os campos permitidos e remove valores vazios
        $insertData = array_filter($bidData, function($value) {
            return $value !== null && $value !== '';
        });

        // Garante que os campos obrigatórios estejam presentes
        $requiredFields = ['deal_id', 'user_id', 'type'];
        foreach ($requiredFields as $field) {
            if (!isset($insertData[$field])) {
                return false;
            }
        }

        // Valida o tipo e ajusta os campos conforme necessário
        if ($insertData['type'] == 1) { // Compra
            if (!isset($insertData['value']) || $insertData['value'] <= 0) {
                return false;
            }
            $insertData['trade_for'] = null;
        } elseif ($insertData['type'] == 2) { // Troca
            if (!isset($insertData['trade_for']) || empty($insertData['trade_for'])) {
                return false;
            }
            $insertData['value'] = null;
        } else {
            return false; // Tipo inválido
        }

        $bidId = $this->insert($insertData);
        if ($bidId) {
            return $this->find($bidId);
        }
        return false;
    }

    public function getBidById(int $id, int $dealId)
    {
        return $this->where('id', $id)
                   ->where('deal_id', $dealId)
                   ->first();
    }

    public function getBidsByDealId(int $dealId)
    {
        return $this->where('deal_id', $dealId)
                   ->where('deleted_at IS NULL')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    public function updateBid(int $id, int $dealId, array $bidData)
    {
        // Remove campos vazios/null para evitar sobrescrever dados existentes
        $updateData = array_filter($bidData, function($value) {
            return $value !== null && $value !== '';
        });

        $result = $this->where('id', $id)
                      ->where('deal_id', $dealId)
                      ->set($updateData)
                      ->update();

        if ($result) {
            return $this->find($id);
        }
        return false;
    }

    public function getBidsByDeal(int $dealId)
    {
        $builder = $this->builder();
        $builder->select('bids.*, users.name as user_name');
        $builder->join('users', 'users.id = bids.user_id');
        $builder->where('bids.deal_id', $dealId);
        $builder->where('bids.deleted_at IS NULL');
        $builder->orderBy('bids.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getUserBidForDeal(int $userId, int $dealId)
    {
        return $this->where('user_id', $userId)
                   ->where('deal_id', $dealId)
                   ->where('deleted_at IS NULL')
                   ->first();
    }

    public function getBidsByUser(int $userId)
    {
        $builder = $this->builder();
        $builder->select('bids.*, deals.type as deal_type, deals.description as deal_description, deals.value as deal_value, users.name as creator_name');
        $builder->join('deals', 'deals.id = bids.deal_id');
        $builder->join('users', 'users.id = deals.user_id');
        $builder->where('bids.user_id', $userId);
        $builder->where('bids.deleted_at IS NULL');
        $builder->orderBy('bids.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
