<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\DeactivateInvoiceDTO;

class DeactivateInvoiceValidator
{
    public function validate(DeactivateInvoiceDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new \InvalidArgumentException('ID inválido');
        }
    }
} 