<?php

namespace App\Application\DTOs\Invoice;

class FindInvoiceByIdDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 