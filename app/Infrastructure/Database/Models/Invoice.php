<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'value',
        'service_description',
        'competence_month',
        'receipt_date',
        'partner_company_id',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'competence_month' => 'date',
        'receipt_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function partnerCompany()
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
} 