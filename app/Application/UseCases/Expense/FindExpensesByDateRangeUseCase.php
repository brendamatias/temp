<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpensesByDateRangeUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false)
    {
        return $this->repository->findByDateRange($startDate, $endDate, $includeInactive);
    }
} 