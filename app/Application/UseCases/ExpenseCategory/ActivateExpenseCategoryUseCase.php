<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;

class ActivateExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->activate($id);
    }
} 