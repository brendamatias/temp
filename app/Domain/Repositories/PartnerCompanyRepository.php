<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\PartnerCompany;

interface PartnerCompanyRepository
{
    public function save(PartnerCompany $company): void;
    public function findById(int $id): ?PartnerCompany;
    public function findAll(int $page = 1, int $perPage = 10, ?bool $includeInactive = null): array;
    public function findByDocument(string $document): ?PartnerCompany;
    public function findByName(string $name): array;
} 