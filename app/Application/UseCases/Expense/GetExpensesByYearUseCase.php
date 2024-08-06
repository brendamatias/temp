<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class GetExpensesByYearUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(int $year, bool $includeInactive = false)
    {
        return $this->repository->getByYear($year, $includeInactive);
    }
} 