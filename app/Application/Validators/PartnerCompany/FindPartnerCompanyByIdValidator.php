<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\FindPartnerCompanyByIdDTO;
use InvalidArgumentException;

class FindPartnerCompanyByIdValidator
{
    public function validate(FindPartnerCompanyByIdDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da empresa deve ser maior que 0');
        }
    }
} 