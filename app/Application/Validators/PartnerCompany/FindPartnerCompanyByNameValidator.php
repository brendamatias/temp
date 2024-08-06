<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\FindPartnerCompanyByNameDTO;
use InvalidArgumentException;

class FindPartnerCompanyByNameValidator
{
    public function validate(FindPartnerCompanyByNameDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da empresa é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da empresa não pode ter mais de 100 caracteres');
        }
    }
} 