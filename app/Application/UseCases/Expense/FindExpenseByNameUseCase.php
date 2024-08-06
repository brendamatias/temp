<?php

namespace App\Application\UseCases\Expense;

use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpenseByNameUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $repository
    ) {}

    public function execute(string $name, bool $includeInactive = false)
    {
        return $this->repository->findByName($name, $includeInactive);
    }
} 