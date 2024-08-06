<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use App\Application\DTOs\ExpenseCategory\FindExpenseCategoryByIdDTO;
use App\Domain\Entities\ExpenseCategory;

class FindExpenseCategoryByIdUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(FindExpenseCategoryByIdDTO $dto): ?ExpenseCategory
    {
        return $this->repository->findById($dto->id);
    }
} 