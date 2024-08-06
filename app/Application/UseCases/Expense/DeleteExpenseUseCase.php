<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;
use InvalidArgumentException;

class DeleteExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(int $id): void
    {
        $expense = $this->expenseRepository->findById($id);
        if (!$expense) {
            throw new InvalidArgumentException(json_encode([
                'id' => ['Despesa não encontrada']
            ]));
        }

        $this->expenseRepository->delete($id);
    }
} 