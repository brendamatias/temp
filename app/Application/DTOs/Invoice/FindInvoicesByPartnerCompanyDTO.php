<?php

namespace App\Application\DTOs\Invoice;

class FindInvoicesByPartnerCompanyDTO
{
    public function __construct(
        public readonly int $partnerCompanyId
    ) {}
} 