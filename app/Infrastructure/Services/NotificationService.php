<?php

namespace App\Infrastructure\Services;

use App\Domain\Entities\Preferences;
use App\Mail\MeiLimitAlert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Infrastructure\Database\Models\User;
use Twilio\Rest\Client;

class NotificationService
{
    private ?Client $twilioClient = null;

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
                $this->sendEmail($message, $currentAmount, $limit, $percentage);
            }

            if ($this->preferences->isNotificationChannelEnabled('sms')) {
                $this->sendSms($message);
            }
        }
    }

    private function sendEmail(string $message, float $currentAmount, float $limit, float $percentage): void
    {
        try {
            $user = User::find($this->preferences->getUserId());
            if ($user && $user->email) {
                Mail::to($user->email)->send(new MeiLimitAlert($currentAmount, $limit, $percentage));
                Log::info('Email notification sent to: ' . $user->email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }

    private function sendSms(string $message): void
    {
        try {
            $user = User::find($this->preferences->getUserId());
            if (!$user || !$user->phone) {
                Log::warning('SMS notification skipped: User has no phone number');
                return;
            }

            if (!$this->twilioClient) {
                $this->twilioClient = new Client(
                    config('services.twilio.sid'),
                    config('services.twilio.auth_token')
                );
            }

            $this->twilioClient->messages->create(
                $user->phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message
                ]
            );

            Log::info('SMS notification sent to: ' . $user->phone);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification: ' . $e->getMessage());
        }
    }
} 