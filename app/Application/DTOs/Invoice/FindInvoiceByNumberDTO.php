<?php

namespace App\Application\DTOs\Invoice;

class FindInvoiceByNumberDTO
{
    public function __construct(
        public readonly string $number
    ) {}
} 