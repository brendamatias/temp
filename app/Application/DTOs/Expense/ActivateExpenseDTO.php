<?php

namespace App\Application\DTOs\Expense;

class ActivateExpenseDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 