<?php

namespace App\Application\DTOs\PartnerCompany;

class CreatePartnerCompanyDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $legalName,
        public readonly string $document
    ) {}
} 