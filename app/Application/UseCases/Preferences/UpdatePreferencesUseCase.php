<?php

namespace App\Application\UseCases\Preferences;

use App\Domain\Repositories\PreferencesRepository;
use App\Application\DTOs\Preferences\UpdatePreferencesDTO;
use App\Application\Validators\Preferences\UpdatePreferencesValidator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UpdatePreferencesUseCase
{
    public function __construct(
        private readonly PreferencesRepository $preferencesRepository,
        private readonly UpdatePreferencesValidator $validator
    ) {}

    public function execute(UpdatePreferencesDTO $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $preferences = $this->preferencesRepository->findById($dto->id);
        
        if (!$preferences) {
            $preferences = new \App\Domain\Entities\Preferences();
            $preferences->setId($dto->id);
            $preferences->setUserId($dto->id);
        }

        if ($preferences->getUserId() !== auth()->id()) {
            throw new AccessDeniedHttpException('Acesso não autorizado às preferências');
        }

        if ($dto->theme !== null) {
            $preferences->setOption('theme', $dto->theme);
        }

        if ($dto->language !== null) {
            $preferences->setOption('language', $dto->language);
        }

        if ($dto->currency !== null) {
            $preferences->setOption('currency', $dto->currency);
        }

        if ($dto->date_format !== null) {
            $preferences->setOption('date_format', $dto->date_format);
        }

        if ($dto->time_format !== null) {
            $preferences->setOption('time_format', $dto->time_format);
        }

        if ($dto->notifications_enabled !== null) {
            $preferences->setOption('notifications_enabled', $dto->notifications_enabled);
        }

        if ($dto->email_notifications !== null) {
            $preferences->setOption('email_notifications', $dto->email_notifications);
        }

        if ($dto->sms_notifications !== null) {
            $preferences->setOption('sms_notifications', $dto->sms_notifications);
        }

        if ($dto->mei_annual_limit !== null) {
            $preferences->setOption('mei_annual_limit', $dto->mei_annual_limit);
        }

        if ($dto->mei_alert_threshold !== null) {
            $preferences->setOption('mei_alert_threshold', $dto->mei_alert_threshold);
        }

        if ($dto->mei_monthly_alert_day !== null) {
            $preferences->setOption('mei_monthly_alert_day', $dto->mei_monthly_alert_day);
        }

        $this->preferencesRepository->save($preferences);
    }
} 