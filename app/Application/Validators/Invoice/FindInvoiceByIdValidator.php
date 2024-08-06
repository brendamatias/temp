<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\FindInvoiceByIdDTO;

class FindInvoiceByIdValidator
{
    public function validate(FindInvoiceByIdDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new \InvalidArgumentException('ID inválido');
        }
    }
} 