<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class GetExpensesByTagsUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(bool $includeInactive = false)
    {
        return $this->repository->getByTags($includeInactive);
    }
} 