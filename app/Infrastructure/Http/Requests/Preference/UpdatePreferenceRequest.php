<?php

namespace App\Infrastructure\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'theme' => ['sometimes', 'string', 'in:LIGHT,DARK'],
            'language' => ['sometimes', 'string', 'in:pt-BR,en-US'],
            'currency' => ['sometimes', 'string', 'in:BRL,USD'],
            'date_format' => ['sometimes', 'string', 'in:d/m/Y,Y-m-d'],
            'time_format' => ['sometimes', 'string', 'in:H:i,h:i A'],
            'notifications_enabled' => ['sometimes', 'boolean'],
            'email_notifications' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
            'mei_annual_limit' => ['sometimes', 'numeric', 'min:0', 'max:81000'],
            'mei_alert_threshold' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'mei_monthly_alert_day' => ['sometimes', 'integer', 'min:1', 'max:31']
        ];
    }

    public function messages(): array
    {
        return [
            'theme.in' => 'O tema deve ser LIGHT ou DARK',
            'language.in' => 'O idioma deve ser pt-BR ou en-US',
            'currency.in' => 'A moeda deve ser BRL ou USD',
            'date_format.in' => 'O formato de data deve ser d/m/Y ou Y-m-d',
            'time_format.in' => 'O formato de hora deve ser H:i ou h:i A',
            'notifications_enabled.boolean' => 'O status das notificações deve ser verdadeiro ou falso',
            'email_notifications.boolean' => 'O status das notificações por email deve ser verdadeiro ou falso',
            'sms_notifications.boolean' => 'O status das notificações por SMS deve ser verdadeiro ou falso',
            'mei_annual_limit.numeric' => 'O limite anual do MEI deve ser um número',
            'mei_annual_limit.min' => 'O limite anual do MEI não pode ser negativo',
            'mei_annual_limit.max' => 'O limite anual do MEI não pode ser maior que 81.000',
            'mei_alert_threshold.integer' => 'O limite de alerta do MEI deve ser um número inteiro',
            'mei_alert_threshold.min' => 'O limite de alerta do MEI deve ser no mínimo 1%',
            'mei_alert_threshold.max' => 'O limite de alerta do MEI deve ser no máximo 100%',
            'mei_monthly_alert_day.integer' => 'O dia do alerta mensal deve ser um número inteiro',
            'mei_monthly_alert_day.min' => 'O dia do alerta mensal deve ser entre 1 e 31',
            'mei_monthly_alert_day.max' => 'O dia do alerta mensal deve ser entre 1 e 31'
        ];
    }
} 