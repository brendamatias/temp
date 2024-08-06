<?php

namespace App\Application\DTOs\PartnerCompany;

class ListPartnerCompaniesDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 10
    ) {}
} 