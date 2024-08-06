<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\ActivatePartnerCompanyDTO;
use InvalidArgumentException;

class ActivatePartnerCompanyValidator
{
    public function validate(ActivatePartnerCompanyDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da empresa deve ser maior que 0');
        }
    }
} 