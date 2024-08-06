<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\DeletePartnerCompanyDTO;
use InvalidArgumentException;

class DeletePartnerCompanyValidator
{
    public function validate(DeletePartnerCompanyDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da empresa deve ser maior que 0');
        }
    }
} 