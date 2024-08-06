<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'value',
        'payment_date',
        'competence_date',
        'category_id',
        'partner_company_id',
        'invoice_id',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_end_date',
        'is_active',
        'is_paid'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'payment_date' => 'date',
        'competence_date' => 'date',
        'recurrence_end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'is_paid' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function partnerCompany()
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
} 