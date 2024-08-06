<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'application_id', 'channel_type', 'send_type', 'title', 'message', 
        'recipients', 'metadata', 'status', 'sent_at', 'delivered_at', 
        'read_at', 'error_message'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'application_id' => 'required|integer',
        'channel_type'   => 'required|in_list[webpush,email,sms]',
        'send_type'      => 'required|in_list[api,manual]',
        'message'        => 'required',
        'recipients'     => 'required',
        'status'         => 'in_list[pending,sent,failed,delivered,read]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeJsonFields'];
    protected $beforeUpdate   = ['encodeJsonFields'];
    protected $afterFind      = ['decodeJsonFields'];

    protected function encodeJsonFields(array $data)
    {
        if (isset($data['data']['recipients']) && is_array($data['data']['recipients'])) {
            $data['data']['recipients'] = json_encode($data['data']['recipients']);
        }
        
        if (isset($data['data']['metadata']) && is_array($data['data']['metadata'])) {
            $data['data']['metadata'] = json_encode($data['data']['metadata']);
        }

        return $data;
    }

    protected function decodeJsonFields(array $data)
    {
        if (isset($data['data']['recipients']) && is_string($data['data']['recipients'])) {
            $data['data']['recipients'] = json_decode($data['data']['recipients'], true);
        }
        
        if (isset($data['data']['metadata']) && is_string($data['data']['metadata'])) {
            $data['data']['metadata'] = json_decode($data['data']['metadata'], true);
        }

        return $data;
    }

    public function getNotificationsByApplication($applicationId, $filters = [])
    {
        $builder = $this->where('application_id', $applicationId);

        if (isset($filters['channel_type'])) {
            $builder->where('channel_type', $filters['channel_type']);
        }

        if (isset($filters['send_type'])) {
            $builder->where('send_type', $filters['send_type']);
        }

        if (isset($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to']);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getNotificationStats($applicationId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('status, COUNT(*) as count');
        $builder->where('application_id', $applicationId);
        $builder->groupBy('status');
        
        return $builder->get()->getResultArray();
    }
}