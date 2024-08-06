<?php

namespace App\Application\UseCases\Expense;

use App\Application\DTOs\Expense\UpdateExpenseDTO;
use App\Domain\Entities\Expense;
use App\Domain\Repositories\ExpenseRepositoryInterface;

class UpdateExpenseUseCase
{
    public function __construct(
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(int $id, UpdateExpenseDTO $dto): Expense
    {
        $expense = $this->expenseRepository->findById($id);

        if (!$expense) {
            throw new \InvalidArgumentException(json_encode(['id' => ['Despesa não encontrada']]));
        }

        if ($dto->name !== null) {
            $expense->setName($dto->name);
        }
        if ($dto->value !== null) {
            $expense->setValue($dto->value);
        }
        if ($dto->categoryId !== null) {
            $expense->setCategoryId($dto->categoryId);
        }
        if ($dto->paymentDate !== null) {
            $expense->setPaymentDate($dto->paymentDate);
        }
        if ($dto->competenceDate !== null) {
            $expense->setCompetenceDate($dto->competenceDate);
        }
        if ($dto->partnerCompanyId !== null) {
            $expense->setPartnerCompanyId($dto->partnerCompanyId);
        }
        if ($dto->invoiceId !== null) {
            $expense->setInvoiceId($dto->invoiceId);
        }
        if ($dto->isPaid !== null) {
            $expense->setIsPaid($dto->isPaid);
        }

        return $this->expenseRepository->update($expense);
    }
} 