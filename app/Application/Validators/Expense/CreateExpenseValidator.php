<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\CreateExpenseDTO;
use InvalidArgumentException;

class CreateExpenseValidator
{
    public function validate(CreateExpenseDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da despesa é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da despesa não pode ter mais de 100 caracteres');
        }

        if ($dto->value <= 0) {
            throw new InvalidArgumentException('O valor da despesa deve ser maior que 0');
        }

        if ($dto->paymentDate === null) {
            throw new InvalidArgumentException('A data de pagamento é obrigatória');
        }

        if ($dto->competenceDate === null) {
            throw new InvalidArgumentException('A data de competência é obrigatória');
        }

        if ($dto->categoryId <= 0) {
            throw new InvalidArgumentException('A categoria é obrigatória');
        }
    }
} 