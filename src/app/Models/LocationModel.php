<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table            = 'locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lat',
        'lng',
        'address',
        'city',
        'state',
        'zip_code'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'lat'      => 'permit_empty|decimal',
        'lng'      => 'permit_empty|decimal',
        'address'  => 'permit_empty|min_length[5]|max_length[500]',
        'city'     => 'required|min_length[2]|max_length[100]',
        'state'    => 'required|min_length[2]|max_length[100]',
        'zip_code' => 'permit_empty|integer|greater_than[0]'
    ];
    
    protected $validationMessages   = [
        'lat' => [
            'required' => 'A latitude é obrigatória',
            'decimal' => 'A latitude deve ser um número decimal'
        ],
        'lng' => [
            'required' => 'A longitude é obrigatória',
            'decimal' => 'A longitude deve ser um número decimal'
        ],
        'address' => [
            'required' => 'O endereço é obrigatório',
            'min_length' => 'O endereço deve ter pelo menos 5 caracteres',
            'max_length' => 'O endereço não pode exceder 500 caracteres'
        ],
        'city' => [
            'required' => 'A cidade é obrigatória',
            'min_length' => 'A cidade deve ter pelo menos 2 caracteres',
            'max_length' => 'A cidade não pode exceder 100 caracteres'
        ],
        'state' => [
            'required' => 'O estado é obrigatório',
            'min_length' => 'O estado deve ter pelo menos 2 caracteres',
            'max_length' => 'O estado não pode exceder 100 caracteres'
        ],
        'zip_code' => [
            'required' => 'O CEP é obrigatório',
            'integer' => 'O CEP deve ser um número inteiro',
            'greater_than' => 'O CEP deve ser maior que zero'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function createLocation($locationData)
    {
        $processedData = [
            'lat' => !empty($locationData['lat']) && $locationData['lat'] != '0' ? (float)$locationData['lat'] : 0.0,
            'lng' => !empty($locationData['lng']) && $locationData['lng'] != '0' ? (float)$locationData['lng'] : 0.0,
            'address' => !empty($locationData['address']) ? $locationData['address'] : 'Endereço não informado',
            'city' => $locationData['city'],
            'state' => $locationData['state'],
            'zip_code' => !empty($locationData['zip_code']) ? (int)$locationData['zip_code'] : 0
        ];
        
        if (empty($processedData['city']) || empty($processedData['state'])) {
            return false;
        }
        
        $locationId = $this->insert($processedData);
        
        if ($locationId) {
            return $this->find($locationId);
        }
        
        return false;
    }

    public function getLocationById($id)
    {
        $location = $this->find($id);
        
        if ($location) {
            return [
                'lat' => (float)$location['lat'],
                'lng' => (float)$location['lng'],
                'address' => $location['address'],
                'city' => $location['city'],
                'state' => $location['state'],
                'zip_code' => (int)$location['zip_code']
            ];
        }
        
        return false;
    }
}
