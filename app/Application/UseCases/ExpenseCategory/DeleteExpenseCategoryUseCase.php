<?php

namespace App\Application\UseCases\ExpenseCategory;

use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;

class DeleteExpenseCategoryUseCase
{
    public function __construct(
        private readonly ExpenseCategoryRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        $category = $this->repository->findById($id);
        
        if ($category === null) {
            throw new \InvalidArgumentException('Categoria não encontrada');
        }

        return $this->repository->delete($id);
    }
} 