<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\ListExpenseCategoriesDTO;
use InvalidArgumentException;

class ListExpenseCategoriesValidator
{
    public function validate(ListExpenseCategoriesDTO $dto): void
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
    }
} 