<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByAmountRangeDTO;

class FindInvoicesByAmountRangeValidator
{
    public function validate(FindInvoicesByAmountRangeDTO $dto): void
    {
        if ($dto->minAmount < 0) {
            throw new \InvalidArgumentException('Valor mínimo não pode ser negativo');
        }

        if ($dto->maxAmount < $dto->minAmount) {
            throw new \InvalidArgumentException('Valor máximo não pode ser menor que o valor mínimo');
        }
    }
} 