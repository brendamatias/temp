<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepository;

class FindPartnerCompanyByNameUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(string $name): array
    {
        return $this->repository->findByName($name);
    }
} 