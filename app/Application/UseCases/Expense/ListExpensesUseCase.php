<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class ListExpensesUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(bool $includeInactive = false): array
    {
        return $this->expenseRepository->all($includeInactive);
    }
} 