<?php

namespace App\Application\DTOs\Invoice;

class ListInvoicesDTO
{
    public function __construct(
        public readonly bool $includeInactive = false
    ) {}
} 