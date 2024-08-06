<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\DeleteExpenseDTO;
use InvalidArgumentException;

class DeleteExpenseValidator
{
    public function validate(DeleteExpenseDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da despesa deve ser maior que 0');
        }
    }
} 