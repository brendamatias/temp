<?php

namespace App\Models;

use CodeIgniter\Model;

class DealModel extends Model
{
    protected $table = 'deals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'user_id',
        'type',
        'value',
        'description',
        'trade_for',
        'location_id',
        'urgency_type',
        'urgency_limit_date',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function createDeal(array $dealData, int $locationId)
    {
        $insertData = [
            'user_id' => $dealData['user_id'],
            'type' => $dealData['type'],
            'value' => $dealData['value'],
            'description' => $dealData['description'],
            'trade_for' => $dealData['trade_for'],
            'location_id' => $locationId,
            'urgency_type' => $dealData['urgency_type'],
            'urgency_limit_date' => $dealData['urgency_limit_date'] ?? null,
        ];

        $dealId = $this->insert($insertData);
        if ($dealId) {
            return $this->find($dealId);
        }
        return false;
    }

    public function getActiveDeals()
    {
        $builder = $this->builder();
        $builder->select('deals.*, locations.lat, locations.lng, locations.address, locations.city, locations.state, locations.zip_code, users.name as creator_name');
        $builder->join('locations', 'locations.id = deals.location_id');
        $builder->join('users', 'users.id = deals.user_id');
        $builder->where('deals.deleted_at IS NULL');
        $builder->orderBy('deals.created_at', 'DESC');
        $builder->limit(10);
        
        return $builder->get()->getResultArray();
    }

    public function getDealsByUser(int $userId)
    {
        $builder = $this->builder();
        $builder->select('deals.*, locations.lat, locations.lng, locations.address, locations.city, locations.state, locations.zip_code');
        $builder->join('locations', 'locations.id = deals.location_id');
        $builder->where('deals.user_id', $userId);
        $builder->where('deals.deleted_at IS NULL');
        $builder->orderBy('deals.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function updateDeal(int $id, array $dealData)
    {
        $updateData = [
            'type' => $dealData['type'],
            'value' => $dealData['value'],
            'description' => $dealData['description'],
            'trade_for' => $dealData['trade_for'],
            'urgency_type' => $dealData['urgency_type'],
            'urgency_limit_date' => $dealData['urgency_limit_date'] ?? null,
        ];

        $result = $this->where('id', $id)->set($updateData)->update();
        if ($result) {
            return $this->find($id);
        }
        return false;
    }

    public function getDealById(int $id)
    {
        $builder = $this->builder();
        $builder->select('deals.*, locations.lat, locations.lng, locations.address, locations.city, locations.state, locations.zip_code, users.name as user_name, users.created_at as user_created_at');
        $builder->join('locations', 'locations.id = deals.location_id');
        $builder->join('users', 'users.id = deals.user_id');
        $builder->where('deals.id', $id);
        
        return $builder->get()->getRowArray();
    }

    public function searchDeals(array $filters)
    {
        $builder = $this->builder();
        $builder->select('deals.*, locations.lat, locations.lng, locations.address, locations.city, locations.state, locations.zip_code, users.name as user_name');
        $builder->join('locations', 'locations.id = deals.location_id');
        $builder->join('users', 'users.id = deals.user_id');
        $builder->where('deals.deleted_at IS NULL');

        // Filtro por tipo (venda/troca)
        if (!empty($filters['type'])) {
            $builder->where('deals.type', $filters['type']);
        }

        // Filtro por faixa de preço
        if (!empty($filters['min_price'])) {
            $builder->where('deals.value >=', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $builder->where('deals.value <=', $filters['max_price']);
        }

        // Filtro por localização
        if (!empty($filters['location'])) {
            $builder->groupStart();
            $builder->like('locations.city', $filters['location']);
            $builder->orLike('locations.state', $filters['location']);
            $builder->orLike('locations.address', $filters['location']);
            $builder->groupEnd();
        }

        // Filtro por termo de busca
        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('deals.description', $filters['search']);
            $builder->orLike('deals.trade_for', $filters['search']);
            $builder->groupEnd();
        }

        $builder->orderBy('deals.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}
