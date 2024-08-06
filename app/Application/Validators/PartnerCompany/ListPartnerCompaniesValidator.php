<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\ListPartnerCompaniesDTO;
use InvalidArgumentException;

class ListPartnerCompaniesValidator
{
    public function validate(ListPartnerCompaniesDTO $dto): void
    {
        if ($dto->page < 1) {
            throw new InvalidArgumentException('A página deve ser maior que 0');
        }

        if ($dto->perPage < 1 || $dto->perPage > 100) {
            throw new InvalidArgumentException('O número de itens por página deve estar entre 1 e 100');
        }

        if ($dto->search !== null && strlen($dto->search) > 100) {
            throw new InvalidArgumentException('O termo de busca não pode ter mais de 100 caracteres');
        }
    }
} 