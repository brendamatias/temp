<?php

namespace App\Infrastructure\Http\Requests\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:1000']
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome da categoria deve ser um texto',
            'name.max' => 'O nome da categoria não pode ter mais de 255 caracteres',
            'description.string' => 'A descrição da categoria deve ser um texto',
            'description.max' => 'A descrição da categoria não pode ter mais de 1000 caracteres'
        ];
    }
} 