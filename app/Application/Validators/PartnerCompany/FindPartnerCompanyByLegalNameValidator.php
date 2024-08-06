<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\FindPartnerCompanyByLegalNameDTO;
use InvalidArgumentException;

class FindPartnerCompanyByLegalNameValidator
{
    public function validate(FindPartnerCompanyByLegalNameDTO $dto): void
    {
        if (empty($dto->legalName)) {
            throw new InvalidArgumentException('O nome fantasia da empresa é obrigatório');
        }

        if (strlen($dto->legalName) > 100) {
            throw new InvalidArgumentException('O nome fantasia da empresa não pode ter mais de 100 caracteres');
        }
    }
} 