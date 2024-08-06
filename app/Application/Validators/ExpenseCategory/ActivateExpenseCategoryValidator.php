<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\ActivateExpenseCategoryDTO;
use InvalidArgumentException;

class ActivateExpenseCategoryValidator
{
    public function validate(ActivateExpenseCategoryDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da categoria deve ser maior que 0');
        }
    }
} 