<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpensesByCompetenceDateRangeUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false)
    {
        return $this->repository->findByCompetenceDateRange($startDate, $endDate, $includeInactive);
    }
} 