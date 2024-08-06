<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\FindExpenseCategoryByNameDTO;
use InvalidArgumentException;

class FindExpenseCategoryByNameValidator
{
    public function validate(FindExpenseCategoryByNameDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da categoria é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da categoria não pode ter mais de 100 caracteres');
        }
    }
} 