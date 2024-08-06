<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\UpdateExpenseDTO;
use InvalidArgumentException;

class UpdateExpenseValidator
{
    public function validate(UpdateExpenseDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da despesa deve ser maior que 0');
        }

        if ($dto->name !== null) {
            if (empty($dto->name)) {
                throw new InvalidArgumentException('O nome da despesa não pode ser vazio');
            }

            if (strlen($dto->name) > 100) {
                throw new InvalidArgumentException('O nome da despesa não pode ter mais de 100 caracteres');
            }
        }

        if ($dto->value !== null && $dto->value <= 0) {
            throw new InvalidArgumentException('O valor da despesa deve ser maior que 0');
        }

        if ($dto->categoryId !== null && $dto->categoryId <= 0) {
            throw new InvalidArgumentException('A categoria é inválida');
        }
    }
} 