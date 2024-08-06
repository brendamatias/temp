<?php

namespace App\Application\DTOs\ExpenseCategory;

class CreateExpenseCategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description
    ) {}
} 