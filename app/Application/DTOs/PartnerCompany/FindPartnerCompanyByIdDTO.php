<?php

namespace App\Application\DTOs\PartnerCompany;

class FindPartnerCompanyByIdDTO
{
    public function __construct(
        public readonly int $id
    ) {}
} 