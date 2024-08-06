<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;

class FindExpenseCategoryByNameUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(string $name, bool $includeInactive = false)
    {
        return $this->repository->findByName($name, $includeInactive);
    }
} 