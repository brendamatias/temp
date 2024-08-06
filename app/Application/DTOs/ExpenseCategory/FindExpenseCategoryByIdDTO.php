<?php

namespace App\Application\DTOs\ExpenseCategory;

class FindExpenseCategoryByIdDTO
{
    public function __construct(
        public readonly int $id,
        public readonly bool $includeInactive = false
    ) {}
} 