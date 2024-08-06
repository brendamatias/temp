<?php

namespace App\Application\UseCases\Expense;

use App\Application\DTOs\Expense\CreateExpenseDTO;
use App\Domain\Entities\Expense;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use InvalidArgumentException;

class CreateExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository,
        private readonly ExpenseCategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CreateExpenseDTO $dto): Expense
    {
        $category = $this->categoryRepository->findById($dto->getCategoryId());
        if (!$category) {
            throw new InvalidArgumentException(json_encode([
                'category_id' => ['A categoria selecionada não existe']
            ]));
        }

        if (!$category->isActive()) {
            throw new InvalidArgumentException(json_encode([
                'category_id' => ['A categoria selecionada está inativa']
            ]));
        }

        $expense = new Expense(
            name: $dto->name,
            value: $dto->value,
            categoryId: $dto->categoryId,
            paymentDate: $dto->paymentDate,
            competenceDate: $dto->competenceDate,
            partnerCompanyId: $dto->partnerCompanyId,
            invoiceId: $dto->invoiceId,
            isPaid: $dto->isPaid
        );

        return $this->expenseRepository->create($expense);
    }
} 