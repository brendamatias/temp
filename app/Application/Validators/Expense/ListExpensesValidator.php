<?php

namespace App\Application\Validators\Expense;

use App\Application\DTOs\Expense\ListExpensesDTO;
use InvalidArgumentException;

class ListExpensesValidator
{
    public function validate(ListExpensesDTO $dto): void
    {
        if ($dto->page <= 0) {
            throw new InvalidArgumentException('A página deve ser maior que 0');
        }

        if ($dto->perPage < 1 || $dto->perPage > 100) {
            throw new InvalidArgumentException('O número de itens por página deve estar entre 1 e 100');
        }

        if ($dto->search !== null && strlen($dto->search) > 100) {
            throw new InvalidArgumentException('O termo de busca não pode ter mais de 100 caracteres');
        }

        if ($dto->startDate !== null && $dto->endDate !== null && $dto->startDate > $dto->endDate) {
            throw new InvalidArgumentException('A data inicial não pode ser maior que a data final');
        }

        if ($dto->startCompetenceDate !== null && $dto->endCompetenceDate !== null && $dto->startCompetenceDate > $dto->endCompetenceDate) {
            throw new InvalidArgumentException('A data de competência inicial não pode ser maior que a data de competência final');
        }
    }
} 