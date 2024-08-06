<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepositoryInterface;

class FindPartnerCompanyByLegalNameUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepositoryInterface $repository
    ) {}

    public function execute(string $legalName, bool $includeInactive = false)
    {
        return $this->repository->findByLegalName($legalName, $includeInactive);
    }
} 