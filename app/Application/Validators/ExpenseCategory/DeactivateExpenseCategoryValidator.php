<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\DeactivateExpenseCategoryDTO;
use InvalidArgumentException;

class DeactivateExpenseCategoryValidator
{
    public function validate(DeactivateExpenseCategoryDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da categoria deve ser maior que 0');
        }
    }
} 