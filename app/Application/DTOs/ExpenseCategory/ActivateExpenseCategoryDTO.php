<?php

namespace App\Application\DTOs\ExpenseCategory;

class ActivateExpenseCategoryDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 