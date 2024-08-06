<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByPartnerCompanyDTO;

class FindInvoicesByPartnerCompanyValidator
{
    public function validate(FindInvoicesByPartnerCompanyDTO $dto): void
    {
        if ($dto->partnerCompanyId <= 0) {
            throw new \InvalidArgumentException('ID da empresa parceira inválido');
        }
    }
} 