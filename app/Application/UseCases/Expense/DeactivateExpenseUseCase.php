<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;
use InvalidArgumentException;

class DeactivateExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(int $id): bool
    {
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            throw new \Exception('Despesa não encontrada');
        }

        $expense->deactivate();
        $this->expenseRepository->update($expense);

        return true;
    }
} 