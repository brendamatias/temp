<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table            = 'applications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name', 'api_key', 'api_secret', 'is_active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|integer',
        'name'    => 'required|min_length[3]|max_length[255]',
        'is_active'  => 'in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateApiCredentials'];

    protected function generateApiCredentials(array $data)
    {
        if (!isset($data['data']['api_key'])) {
            $data['data']['api_key'] = bin2hex(random_bytes(16));
        }
        
        if (!isset($data['data']['api_secret'])) {
            $data['data']['api_secret'] = bin2hex(random_bytes(32));
        }

        return $data;
    }

    public function getApplicationsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getApplicationWithChannels($id)
    {
        $builder = $this->db->table('applications a');
        $builder->select('a.*, nc.channel_type, nc.is_enabled');
        $builder->join('notification_channels nc', 'nc.application_id = a.id', 'left');
        $builder->where('a.id', $id);
        
        return $builder->get()->getResultArray();
    }
}