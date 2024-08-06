<?php

namespace App\Application\DTOs\Expense;

class CreateExpenseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $value,
        public readonly int $categoryId,
        public readonly \DateTime $paymentDate,
        public readonly \DateTime $competenceDate,
        public readonly ?int $partnerCompanyId = null,
        public readonly ?int $invoiceId = null,
        public readonly bool $isPaid = false
    ) {}

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
} 