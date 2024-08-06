<?php

namespace App\Application\DTOs\ExpenseCategory;

class DeleteExpenseCategoryDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 