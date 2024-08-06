<?php

namespace App\Application\DTOs\Invoice;

class FindInvoicesByAmountRangeDTO
{
    public function __construct(
        public readonly float $minAmount,
        public readonly float $maxAmount
    ) {}
} 