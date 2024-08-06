<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\CreateExpenseCategoryDTO;
use InvalidArgumentException;

class CreateExpenseCategoryValidator
{
    public function validate(CreateExpenseCategoryDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da categoria é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da categoria não pode ter mais de 100 caracteres');
        }

        if (empty($dto->description)) {
            throw new InvalidArgumentException('A descrição da categoria é obrigatória');
        }

        if (strlen($dto->description) > 500) {
            throw new InvalidArgumentException('A descrição da categoria não pode ter mais de 500 caracteres');
        }
    }
} 