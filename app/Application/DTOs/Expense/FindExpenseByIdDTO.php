<?php

namespace App\Application\DTOs\Expense;

class FindExpenseByIdDTO
{
    public function __construct(
        private readonly int $id
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
} 