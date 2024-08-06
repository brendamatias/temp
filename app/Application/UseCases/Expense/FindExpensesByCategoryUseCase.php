<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpensesByCategoryUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(int $categoryId, bool $includeInactive = false)
    {
        return $this->repository->findByCategory($categoryId, $includeInactive);
    }
} 