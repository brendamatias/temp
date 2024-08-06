<?php

namespace App\Application\DTOs\Expense;

class UpdateExpenseDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?float $value = null,
        public readonly ?int $categoryId = null,
        public readonly ?\DateTime $paymentDate = null,
        public readonly ?\DateTime $competenceDate = null,
        public readonly ?int $partnerCompanyId = null,
        public readonly ?int $invoiceId = null,
        public readonly ?bool $isPaid = null
    ) {}
} 