<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Entities\ExpenseCategory;
use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use App\Application\DTOs\ExpenseCategory\CreateExpenseCategoryDTO;

class CreateExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(CreateExpenseCategoryDTO $dto): ExpenseCategory
    {
        $category = new ExpenseCategory($dto->name, $dto->description);
        return $this->repository->create($category);
    }
} 