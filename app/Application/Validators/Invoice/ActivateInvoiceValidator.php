<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\ActivateInvoiceDTO;

class ActivateInvoiceValidator
{
    public function validate(ActivateInvoiceDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new \InvalidArgumentException('ID inválido');
        }
    }
} 