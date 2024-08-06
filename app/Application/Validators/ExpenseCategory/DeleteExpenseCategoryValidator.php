<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\DeleteExpenseCategoryDTO;
use InvalidArgumentException;

class DeleteExpenseCategoryValidator
{
    public function validate(DeleteExpenseCategoryDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da categoria deve ser maior que 0');
        }
    }
} 