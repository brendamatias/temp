<?php

namespace App\Application\DTOs\Expense;

class FindExpenseByNameDTO
{
    public function __construct(
        public readonly string $name,
        public readonly bool $includeInactive = false
    ) {}
} 