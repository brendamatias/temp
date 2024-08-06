<?php

namespace App\Application\Validators\Preferences;

use App\Application\DTOs\Preferences\UpdatePreferencesDTO;

class UpdatePreferencesValidator
{
    public function validate(UpdatePreferencesDTO $dto): array
    {
        $errors = [];

        if ($dto->theme !== null && !in_array($dto->theme, ['LIGHT', 'DARK'])) {
            $errors['theme'][] = 'O tema deve ser LIGHT ou DARK';
        }

        if ($dto->language !== null && !preg_match('/^[a-z]{2}-[A-Z]{2}$/', $dto->language)) {
            $errors['language'][] = 'O idioma deve estar no formato pt-BR';
        }

        if ($dto->currency !== null && !preg_match('/^[A-Z]{3}$/', $dto->currency)) {
            $errors['currency'][] = 'A moeda deve estar no formato BRL';
        }

        if ($dto->date_format !== null && !in_array($dto->date_format, ['d/m/Y', 'Y-m-d'])) {
            $errors['date_format'][] = 'O formato de data deve ser d/m/Y ou Y-m-d';
        }

        if ($dto->time_format !== null && !in_array($dto->time_format, ['H:i', 'h:i A'])) {
            $errors['time_format'][] = 'O formato de hora deve ser H:i ou h:i A';
        }

        if ($dto->notifications_enabled !== null && !is_bool($dto->notifications_enabled)) {
            $errors['notifications_enabled'][] = 'O status das notificações deve ser booleano';
        }

        if ($dto->email_notifications !== null && !is_bool($dto->email_notifications)) {
            $errors['email_notifications'][] = 'O status das notificações por email deve ser booleano';
        }

        if ($dto->sms_notifications !== null && !is_bool($dto->sms_notifications)) {
            $errors['sms_notifications'][] = 'O status das notificações por SMS deve ser booleano';
        }

        if ($dto->mei_annual_limit !== null && $dto->mei_annual_limit <= 0) {
            $errors['mei_annual_limit'][] = 'O limite anual deve ser maior que zero';
        }

        if ($dto->mei_alert_threshold !== null && 
            ($dto->mei_alert_threshold < 0 || $dto->mei_alert_threshold > 100)) {
            $errors['mei_alert_threshold'][] = 'O limite de alerta deve estar entre 0 e 100';
        }

        if ($dto->mei_monthly_alert_day !== null && 
            ($dto->mei_monthly_alert_day < 1 || $dto->mei_monthly_alert_day > 31)) {
            $errors['mei_monthly_alert_day'][] = 'O dia do alerta mensal deve estar entre 1 e 31';
        }

        return $errors;
    }
} 