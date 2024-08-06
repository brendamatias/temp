<?php

namespace App\Application\DTOs\Invoice;

class ActivateInvoiceDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 