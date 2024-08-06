<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\DeactivateExpenseDTO;
use InvalidArgumentException;

class DeactivateExpenseValidator
{
    public function validate(DeactivateExpenseDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da despesa deve ser maior que 0');
        }
    }
} 