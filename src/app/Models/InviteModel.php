<?php

namespace App\Models;

use CodeIgniter\Model;

class InviteModel extends Model
{
    protected $table = 'invites';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'name',
        'email',
        'user',
        'user_invited',
        'status',
        'message',
        'expires_at',
        'max_uses',
        'used_count'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function createInvite(array $inviteData)
    {
        $insertData = [
            'name' => $inviteData['name'],
            'email' => $inviteData['email'],
            'user' => $inviteData['user'],
            'user_invited' => $inviteData['user_invited']
        ];

        $inviteId = $this->insert($insertData);
        if ($inviteId) {
            return $this->find($inviteId);
        }
        return false;
    }

    /**
     * Create a web invite for new users (without user_invited)
     */
    public function createWebInvite(array $inviteData)
    {
        $insertData = [
            'name' => $inviteData['name'],
            'email' => $inviteData['email'],
            'user' => $inviteData['user'],
            'user_invited' => null, // Web invites don't have a user_invited initially
            'status' => 'pending',
            'message' => $inviteData['message'] ?? '',
            'expires_at' => $inviteData['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+7 days')),
            'max_uses' => $inviteData['max_uses'] ?? 1,
            'used_count' => 0
        ];

        $inviteId = $this->insert($insertData);
        if ($inviteId) {
            return $this->find($inviteId);
        }
        return false;
    }

    public function getInvitesByUserId(int $userId)
    {
        return $this->where('user', $userId)
                   ->where('deleted_at IS NULL')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    public function getInviteById(int $id, int $userId)
    {
        return $this->where('id', $id)
                   ->where('user', $userId)
                   ->first();
    }

    public function updateInvite(int $id, int $userId, array $inviteData)
    {
        $updateData = [
            'name' => $inviteData['name'],
            'email' => $inviteData['email'],
            'user_invited' => $inviteData['user_invited']
        ];

        $result = $this->where('id', $id)
                      ->where('user', $userId)
                      ->set($updateData)
                      ->update();

        if ($result) {
            return $this->find($id);
        }
        return false;
    }

    public function getInviteByEmail(string $email)
    {
        return $this->where('email', $email)
                   ->where('deleted_at IS NULL')
                   ->first();
    }

    public function getInvitesByUser(int $userId)
    {
        return $this->getInvitesByUserId($userId);
    }
}
