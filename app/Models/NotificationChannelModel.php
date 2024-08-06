<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationChannelModel extends Model
{
    protected $table            = 'notification_channels';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['application_id', 'channel_type', 'is_enabled', 'configuration'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'application_id' => 'required|integer',
        'channel_type'   => 'required|in_list[webpush,email,sms]',
        'is_enabled'     => 'in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeConfiguration'];
    protected $beforeUpdate   = ['encodeConfiguration'];
    protected $afterFind      = ['decodeConfiguration'];

    protected function encodeConfiguration(array $data)
    {
        if (isset($data['data']['configuration']) && is_array($data['data']['configuration'])) {
            $data['data']['configuration'] = json_encode($data['data']['configuration']);
        }

        return $data;
    }

    protected function decodeConfiguration(array $data)
    {
        if (isset($data['data']['configuration']) && is_string($data['data']['configuration'])) {
            $data['data']['configuration'] = json_decode($data['data']['configuration'], true);
        }

        return $data;
    }

    public function getChannelsByApplication($applicationId)
    {
        return $this->where('application_id', $applicationId)->findAll();
    }

    public function getEnabledChannels($applicationId)
    {
        return $this->where('application_id', $applicationId)
                   ->where('is_enabled', 1)
                   ->findAll();
    }

    public function getChannelByType($applicationId, $channelType)
    {
        return $this->where('application_id', $applicationId)
                   ->where('channel_type', $channelType)
                   ->first();
    }
}