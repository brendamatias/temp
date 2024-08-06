<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;
use InvalidArgumentException;

class GetExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(int $id)
    {
        $expense = $this->expenseRepository->findById($id);
        if (!$expense) {
            throw new InvalidArgumentException(json_encode([
                'id' => ['Despesa não encontrada']
            ]));
        }

        return $expense;
    }
} 