<?php

namespace App\Application\DTOs\Expense;

class DeleteExpenseDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 