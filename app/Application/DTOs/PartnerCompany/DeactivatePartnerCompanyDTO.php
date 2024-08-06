<?php

namespace App\Application\DTOs\PartnerCompany;

class DeactivatePartnerCompanyDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 