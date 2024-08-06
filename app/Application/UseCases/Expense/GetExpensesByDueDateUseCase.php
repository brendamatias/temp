<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class GetExpensesByDueDateUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(bool $includeInactive = false)
    {
        return $this->repository->getByDueDate($includeInactive);
    }
} 