<?php

namespace App\Application\DTOs\ExpenseCategory;

class FindExpenseCategoryByNameDTO
{
    public function __construct(
        public readonly string $name,
        public readonly bool $includeInactive = false
    ) {}
} 