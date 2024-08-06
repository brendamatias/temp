<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\DeactivatePartnerCompanyDTO;
use InvalidArgumentException;

class DeactivatePartnerCompanyValidator
{
    public function validate(DeactivatePartnerCompanyDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da empresa deve ser maior que 0');
        }
    }
} 