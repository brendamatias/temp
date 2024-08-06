<?php

namespace App\Application\UseCases\Expense;

use App\Application\DTOs\Expense\FindExpenseByIdDTO;
use App\Domain\Entities\Expense;
use App\Domain\Repositories\ExpenseRepositoryInterface;

class FindExpenseByIdUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(FindExpenseByIdDTO $dto): ?Expense
    {
        return $this->expenseRepository->findById($dto->getId());
    }
} 