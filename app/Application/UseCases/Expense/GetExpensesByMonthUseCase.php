<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class GetExpensesByMonthUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(int $year, int $month, bool $includeInactive = false)
    {
        return $this->repository->getByMonth($year, $month, $includeInactive);
    }
} 