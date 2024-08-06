<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\FindExpenseByNameDTO;
use InvalidArgumentException;

class FindExpenseByNameValidator
{
    public function validate(FindExpenseByNameDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da despesa é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da despesa não pode ter mais de 100 caracteres');
        }
    }
} 