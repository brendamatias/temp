<?php

namespace App\Application\DTOs\Preferences;

class UpdatePreferencesDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $theme = null,
        public readonly ?string $language = null,
        public readonly ?string $currency = null,
        public readonly ?string $date_format = null,
        public readonly ?string $time_format = null,
        public readonly ?bool $notifications_enabled = null,
        public readonly ?bool $email_notifications = null,
        public readonly ?bool $sms_notifications = null,
        public readonly ?float $mei_annual_limit = null,
        public readonly ?int $mei_alert_threshold = null,
        public readonly ?int $mei_monthly_alert_day = null
    ) {}
} 