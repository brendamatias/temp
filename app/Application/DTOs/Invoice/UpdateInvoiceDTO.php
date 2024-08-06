<?php

namespace App\Application\DTOs\Invoice;

class UpdateInvoiceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $number = null,
        public readonly ?float $value = null,
        public readonly ?string $serviceDescription = null,
        public readonly ?\DateTime $competenceMonth = null,
        public readonly ?\DateTime $receiptDate = null
    ) {}
} 