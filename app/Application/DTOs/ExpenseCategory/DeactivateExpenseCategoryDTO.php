<?php

namespace App\Application\DTOs\ExpenseCategory;

class DeactivateExpenseCategoryDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 