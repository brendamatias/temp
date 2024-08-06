<?php

namespace App\Application\Validators\Invoice;

use App\Application\DTOs\Invoice\ListInvoicesDTO;

class ListInvoicesValidator
{
    public function validate(ListInvoicesDTO $dto): void
    {
        // Não há validações necessárias para este DTO
        // O parâmetro includeInactive é opcional e tem um valor padrão
    }
} 