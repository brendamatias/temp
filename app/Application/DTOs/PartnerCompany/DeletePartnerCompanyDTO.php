<?php

namespace App\Application\DTOs\PartnerCompany;

class DeletePartnerCompanyDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 