<?php

namespace App\Infrastructure\Http\Requests\Preference;

use Illuminate\Foundation\Http\FormRequest;

class CreatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', 'in:LIGHT,DARK'],
            'language' => ['required', 'string', 'in:pt-BR,en-US'],
            'currency' => ['required', 'string', 'in:BRL,USD'],
            'date_format' => ['required', 'string', 'in:d/m/Y,Y-m-d'],
            'time_format' => ['required', 'string', 'in:H:i,h:i A'],
            'notifications_enabled' => ['required', 'boolean'],
            'email_notifications' => ['required', 'boolean'],
            'sms_notifications' => ['required', 'boolean'],
            'mei_annual_limit' => ['required', 'numeric', 'min:0', 'max:81000'],
            'mei_alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'mei_monthly_alert_day' => ['required', 'integer', 'min:1', 'max:31']
        ];
    }

    public function messages(): array
    {
        return [
            'theme.required' => 'O tema é obrigatório',
            'theme.in' => 'O tema deve ser LIGHT ou DARK',
            'language.required' => 'O idioma é obrigatório',
            'language.in' => 'O idioma deve ser pt-BR ou en-US',
            'currency.required' => 'A moeda é obrigatória',
            'currency.in' => 'A moeda deve ser BRL ou USD',
            'date_format.required' => 'O formato de data é obrigatório',
            'date_format.in' => 'O formato de data deve ser d/m/Y ou Y-m-d',
            'time_format.required' => 'O formato de hora é obrigatório',
            'time_format.in' => 'O formato de hora deve ser H:i ou h:i A',
            'notifications_enabled.required' => 'O status das notificações é obrigatório',
            'notifications_enabled.boolean' => 'O status das notificações deve ser verdadeiro ou falso',
            'email_notifications.required' => 'O status das notificações por email é obrigatório',
            'email_notifications.boolean' => 'O status das notificações por email deve ser verdadeiro ou falso',
            'sms_notifications.required' => 'O status das notificações por SMS é obrigatório',
            'sms_notifications.boolean' => 'O status das notificações por SMS deve ser verdadeiro ou falso',
            'mei_annual_limit.required' => 'O limite anual do MEI é obrigatório',
            'mei_annual_limit.numeric' => 'O limite anual do MEI deve ser um número',
            'mei_annual_limit.min' => 'O limite anual do MEI não pode ser negativo',
            'mei_annual_limit.max' => 'O limite anual do MEI não pode ser maior que 81.000',
            'mei_alert_threshold.required' => 'O limite de alerta do MEI é obrigatório',
            'mei_alert_threshold.integer' => 'O limite de alerta do MEI deve ser um número inteiro',
            'mei_alert_threshold.min' => 'O limite de alerta do MEI deve ser no mínimo 1%',
            'mei_alert_threshold.max' => 'O limite de alerta do MEI deve ser no máximo 100%',
            'mei_monthly_alert_day.required' => 'O dia do alerta mensal é obrigatório',
            'mei_monthly_alert_day.integer' => 'O dia do alerta mensal deve ser um número inteiro',
            'mei_monthly_alert_day.min' => 'O dia do alerta mensal deve ser entre 1 e 31',
            'mei_monthly_alert_day.max' => 'O dia do alerta mensal deve ser entre 1 e 31'
        ];
    }
} 