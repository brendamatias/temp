<?php

namespace App\Application\Services;

use App\Domain\Entities\Preferences;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private readonly Preferences $preferences
    ) {}

    public function sendLimitAlert(float $currentAmount, float $limit): void
    {
        $percentage = ($currentAmount / $limit) * 100;
        
        if ($percentage >= $this->preferences->getMeiAlertThreshold()) {
            $message = sprintf(
                'Alerta: Você atingiu %.2f%% do limite anual do MEI (R$ %.2f de R$ %.2f)',
                $percentage,
                $currentAmount,
                $limit
            );

            if ($this->preferences->isNotificationChannelEnabled('email')) {
                $this->sendEmail($message);
            }

            if ($this->preferences->isNotificationChannelEnabled('sms')) {
                $this->sendSms($message);
            }
        }
    }

    private function sendEmail(string $message): void
    {
        try {
            Log::info('Email notification: ' . $message);
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }

    private function sendSms(string $message): void
    {
        try {
            Log::info('SMS notification: ' . $message);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification: ' . $e->getMessage());
        }
    }
} 