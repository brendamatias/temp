<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\ActivateExpenseDTO;
use InvalidArgumentException;

class ActivateExpenseValidator
{
    public function validate(ActivateExpenseDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da despesa deve ser maior que 0');
        }
    }
} 