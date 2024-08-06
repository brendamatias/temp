<?php

namespace App\Application\DTOs\Expense;

class DeactivateExpenseDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 