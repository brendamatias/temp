<?php

namespace App\Infrastructure\Http\Requests\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.string' => 'O nome da categoria deve ser um texto',
            'name.max' => 'O nome da categoria não pode ter mais de 255 caracteres',
            'description.required' => 'A descrição da categoria é obrigatória',
            'description.string' => 'A descrição da categoria deve ser um texto',
            'description.max' => 'A descrição da categoria não pode ter mais de 1000 caracteres'
        ];
    }
} 