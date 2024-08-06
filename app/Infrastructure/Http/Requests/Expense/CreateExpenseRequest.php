<?php

namespace App\Infrastructure\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'competence_date' => 'required|date',
            'category_id' => 'required|integer|exists:expense_categories,id',
            'partner_company_id' => 'nullable|integer|exists:partner_companies,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',
            'is_paid' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da despesa é obrigatório',
            'name.max' => 'O nome da despesa não pode ter mais de 255 caracteres',
            'value.required' => 'O valor da despesa é obrigatório',
            'value.numeric' => 'O valor da despesa deve ser um número',
            'value.min' => 'O valor da despesa deve ser maior que zero',
            'payment_date.required' => 'A data de pagamento é obrigatória',
            'payment_date.date' => 'A data de pagamento deve ser uma data válida',
            'competence_date.required' => 'A data de competência é obrigatória',
            'competence_date.date' => 'A data de competência deve ser uma data válida',
            'category_id.required' => 'A categoria é obrigatória',
            'category_id.exists' => 'A categoria selecionada não existe',
            'partner_company_id.exists' => 'A empresa parceira selecionada não existe',
            'invoice_id.exists' => 'A nota fiscal selecionada não existe',
            'is_paid.boolean' => 'O status de pagamento deve ser verdadeiro ou falso'
        ];
    }
} 