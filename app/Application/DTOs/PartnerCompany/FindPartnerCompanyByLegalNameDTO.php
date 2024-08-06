<?php

namespace App\Application\DTOs\PartnerCompany;

class FindPartnerCompanyByLegalNameDTO
{
    public function __construct(
        public readonly string $legalName,
        public readonly ?bool $includeInactive = false
    ) {}
} 