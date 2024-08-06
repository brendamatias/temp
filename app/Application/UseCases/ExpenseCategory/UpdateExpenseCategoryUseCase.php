<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use App\Application\DTOs\ExpenseCategory\UpdateExpenseCategoryDTO;
use App\Domain\Entities\ExpenseCategory;

class UpdateExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(UpdateExpenseCategoryDTO $dto): ExpenseCategory
    {
        $category = $this->repository->findById($dto->id);
        
        if ($category === null) {
            throw new \InvalidArgumentException('Categoria não encontrada');
        }

        if ($dto->name !== null) {
            $category->setName($dto->name);
        }

        if ($dto->description !== null) {
            $category->setDescription($dto->description);
        }

        return $this->repository->update($category);
    }
} 