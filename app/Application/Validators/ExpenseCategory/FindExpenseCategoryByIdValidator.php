<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\FindExpenseCategoryByIdDTO;
use InvalidArgumentException;

class FindExpenseCategoryByIdValidator
{
    public function validate(FindExpenseCategoryByIdDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da categoria deve ser maior que 0');
        }
    }
} 