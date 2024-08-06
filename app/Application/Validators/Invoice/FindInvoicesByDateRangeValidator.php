<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByDateRangeDTO;

class FindInvoicesByDateRangeValidator
{
    public function validate(FindInvoicesByDateRangeDTO $dto): void
    {
        if ($dto->startDate > $dto->endDate) {
            throw new \InvalidArgumentException('Data inicial não pode ser maior que a data final');
        }
    }
} 