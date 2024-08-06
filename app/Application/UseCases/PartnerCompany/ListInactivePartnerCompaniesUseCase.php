<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepositoryInterface;

class ListInactivePartnerCompaniesUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepositoryInterface $repository
    ) {}

    public function execute()
    {
        return $this->repository->findInactive();
    }
} 