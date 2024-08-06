<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\FindInvoiceByNumberDTO;

class FindInvoiceByNumberValidator
{
    public function validate(FindInvoiceByNumberDTO $dto): void
    {
        if (empty($dto->number)) {
            throw new \InvalidArgumentException('Número da nota fiscal é obrigatório');
        }
    }
} 