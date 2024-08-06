<?php

namespace App\Application\DTOs\Invoice;

class CreateInvoiceDTO
{
    public function __construct(
        public readonly string $number,
        public readonly int $partnerCompanyId,
        public readonly float $value,
        public readonly string $serviceDescription,
        public readonly \DateTime $competenceMonth,
        public readonly \DateTime $receiptDate
    ) {}
} 