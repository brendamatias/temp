<?php

namespace App\Application\DTOs\ExpenseCategory;

class UpdateExpenseCategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $description = null
    ) {}
} 