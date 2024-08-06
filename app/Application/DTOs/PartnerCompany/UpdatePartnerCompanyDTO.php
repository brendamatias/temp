<?php

namespace App\Application\DTOs\PartnerCompany;

class UpdatePartnerCompanyDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $name = null,
        public readonly ?string $legalName = null,
        public readonly ?string $document = null,
        public readonly ?bool $isActive = null
    ) {}
} 