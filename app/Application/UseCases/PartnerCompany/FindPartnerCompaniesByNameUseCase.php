<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepositoryInterface;

class FindPartnerCompaniesByNameUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepositoryInterface $repository
    ) {}

    public function execute(string $name, bool $includeInactive = false)
    {
        return $this->repository->findByName($name, $includeInactive);
    }
} 