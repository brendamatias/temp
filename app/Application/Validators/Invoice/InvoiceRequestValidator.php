<?php

namespace App\Application\Validators\Invoice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceRequestValidator
{
    public function validateCreate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|string',
            'partnerCompanyId' => 'required|integer',
            'value' => 'required|numeric|min:0',
            'service_description' => 'required|string',
            'competence_month' => 'required|date',
            'receipt_date' => 'required|date'
        ], [
            'number.required' => 'O número da nota fiscal é obrigatório',
            'number.string' => 'O número da nota fiscal deve ser um texto',
            'partnerCompanyId.required' => 'A empresa parceira é obrigatória',
            'partnerCompanyId.integer' => 'A empresa parceira deve ser um número inteiro',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.min' => 'O valor deve ser maior que zero',
            'service_description.required' => 'A descrição do serviço é obrigatória',
            'service_description.string' => 'A descrição do serviço deve ser um texto',
            'competence_month.required' => 'O mês de competência é obrigatório',
            'competence_month.date' => 'O mês de competência deve ser uma data válida',
            'receipt_date.required' => 'A data de recebimento é obrigatória',
            'receipt_date.date' => 'A data de recebimento deve ser uma data válida'
        ]);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }

    public function validateUpdate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'number' => 'sometimes|required|string',
            'value' => 'sometimes|required|numeric|min:0',
            'service_description' => 'sometimes|required|string',
            'competence_month' => 'sometimes|required|date',
            'receipt_date' => 'sometimes|required|date'
        ], [
            'number.required' => 'O número da nota fiscal é obrigatório',
            'number.string' => 'O número da nota fiscal deve ser um texto',
            'value.required' => 'O valor é obrigatório',
            'value.numeric' => 'O valor deve ser um número',
            'value.min' => 'O valor deve ser maior que zero',
            'service_description.required' => 'A descrição do serviço é obrigatória',
            'service_description.string' => 'A descrição do serviço deve ser um texto',
            'competence_month.required' => 'O mês de competência é obrigatório',
            'competence_month.date' => 'O mês de competência deve ser uma data válida',
            'receipt_date.required' => 'A data de recebimento é obrigatória',
            'receipt_date.date' => 'A data de recebimento deve ser uma data válida'
        ]);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }
} 