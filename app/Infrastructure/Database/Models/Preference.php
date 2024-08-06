<?php

namespace App\Infrastructure\Database\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'currency',
        'date_format',
        'time_format',
        'notifications_enabled',
        'email_notifications',
        'sms_notifications',
        'mei_annual_limit',
        'mei_alert_threshold',
        'mei_monthly_alert_day'
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'mei_annual_limit' => 'decimal:2',
        'mei_alert_threshold' => 'integer',
        'mei_monthly_alert_day' => 'integer'
    ];

    protected $attributes = [
        'theme' => 'LIGHT',
        'language' => 'pt-BR',
        'currency' => 'BRL',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'notifications_enabled' => true,
        'email_notifications' => true,
        'sms_notifications' => false,
        'mei_annual_limit' => 81000.00,
        'mei_alert_threshold' => 80,
        'mei_monthly_alert_day' => 1
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Infrastructure\Database\Models\User::class);
    }
} 