<?php

namespace App\Application\DTOs\PartnerCompany;

class FindPartnerCompanyByNameDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?bool $includeInactive = false
    ) {}
} 