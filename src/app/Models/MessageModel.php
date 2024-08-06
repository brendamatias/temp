<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'deal_id',
        'user_id',
        'title',
        'message'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function createMessage(array $messageData)
    {
        $insertData = [
            'deal_id' => $messageData['deal_id'],
            'user_id' => $messageData['user_id'],
            'title' => $messageData['title'],
            'message' => $messageData['message']
        ];

        $messageId = $this->insert($insertData);
        if ($messageId) {
            return $this->find($messageId);
        }
        return false;
    }

    public function getMessagesByDealId(int $dealId)
    {
        $builder = $this->builder();
        $builder->select('messages.*, users.name as user_name');
        $builder->join('users', 'users.id = messages.user_id');
        $builder->where('messages.deal_id', $dealId);
        $builder->where('messages.deleted_at IS NULL');
        $builder->orderBy('messages.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getMessageById(int $id, int $dealId)
    {
        return $this->where('id', $id)
                   ->where('deal_id', $dealId)
                   ->first();
    }

    public function updateMessage(int $id, int $dealId, array $messageData)
    {
        $updateData = [
            'user_id' => $messageData['user_id'],
            'title' => $messageData['title'],
            'message' => $messageData['message']
        ];

        $result = $this->where('id', $id)
                      ->where('deal_id', $dealId)
                      ->set($updateData)
                      ->update();

        if ($result) {
            return $this->find($id);
        }
        return false;
    }

    public function getMessagesByUser(int $userId)
    {
        $builder = $this->builder();
        $builder->select('messages.*, deals.type, deals.description as deal_description');
        $builder->join('deals', 'deals.id = messages.deal_id');
        $builder->where('messages.user_id', $userId);
        $builder->where('messages.deleted_at IS NULL');
        $builder->orderBy('messages.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getMessagesByDeal(int $dealId)
    {
        return $this->getMessagesByDealId($dealId);
    }
}
