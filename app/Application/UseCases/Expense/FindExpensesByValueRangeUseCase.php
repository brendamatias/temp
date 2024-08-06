<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpensesByValueRangeUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(float $minValue, float $maxValue, bool $includeInactive = false)
    {
        return $this->repository->findByValueRange($minValue, $maxValue, $includeInactive);
    }
} 