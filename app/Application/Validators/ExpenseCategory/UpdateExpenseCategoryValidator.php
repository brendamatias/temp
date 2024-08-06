<?php

namespace App\Application\Validators\ExpenseCategory;

use App\Application\DTOs\ExpenseCategory\UpdateExpenseCategoryDTO;
use InvalidArgumentException;

class UpdateExpenseCategoryValidator
{
    public function validate(UpdateExpenseCategoryDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da categoria deve ser maior que 0');
        }

        if ($dto->name !== null) {
            if (empty($dto->name)) {
                throw new InvalidArgumentException('O nome da categoria não pode ser vazio');
            }

            if (strlen($dto->name) > 100) {
                throw new InvalidArgumentException('O nome da categoria não pode ter mais de 100 caracteres');
            }
        }

        if ($dto->description !== null) {
            if (empty($dto->description)) {
                throw new InvalidArgumentException('A descrição da categoria não pode ser vazia');
            }

            if (strlen($dto->description) > 500) {
                throw new InvalidArgumentException('A descrição da categoria não pode ter mais de 500 caracteres');
            }
        }
    }
} 