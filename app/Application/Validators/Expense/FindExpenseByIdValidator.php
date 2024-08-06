<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\FindExpenseByIdDTO;
use InvalidArgumentException;

class FindExpenseByIdValidator
{
    public function validate(FindExpenseByIdDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da despesa deve ser maior que 0');
        }
    }
} 