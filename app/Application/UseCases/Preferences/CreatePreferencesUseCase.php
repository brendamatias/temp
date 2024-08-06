<?php

namespace App\Application\UseCases\Preferences;

use App\Application\DTOs\Preferences\CreatePreferencesDTO;
use App\Application\Validators\Preferences\CreatePreferencesValidator;
use App\Domain\Repositories\PreferencesRepository;
use App\Domain\Entities\Preferences;

class CreatePreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly CreatePreferencesValidator $validator
    ) {}

    public function execute(CreatePreferencesDTO $dto): Preferences
    {
        $this->validator->validate($dto);

        $preferences = new Preferences();
        $preferences->setUserId($dto->user_id);

        $structuredOptions = [
            'theme' => $dto->options['theme'] ?? 'LIGHT',
            'language' => $dto->options['language'] ?? 'pt-BR',
            'currency' => $dto->options['currency'] ?? 'BRL',
            'date_format' => $dto->options['date_format'] ?? 'd/m/Y',
            'time_format' => $dto->options['time_format'] ?? 'H:i',
            'notifications_enabled' => $dto->options['notifications_enabled'] ?? true,
            'email_notifications' => $dto->options['email_notifications'] ?? true,
            'sms_notifications' => $dto->options['sms_notifications'] ?? false,
            'mei_annual_limit' => $dto->options['mei_annual_limit'] ?? 81000.00,
            'mei_alert_threshold' => $dto->options['mei_alert_threshold'] ?? 80,
            'mei_monthly_alert_day' => $dto->options['mei_monthly_alert_day'] ?? 1
        ];

        foreach ($structuredOptions as $key => $value) {
            $preferences->setOption($key, $value);
        }

        $this->preferencesRepository->save($preferences);
        return $preferences;
    }
} 