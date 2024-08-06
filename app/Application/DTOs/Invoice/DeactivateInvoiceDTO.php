<?php

namespace App\Application\DTOs\Invoice;

class DeactivateInvoiceDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 