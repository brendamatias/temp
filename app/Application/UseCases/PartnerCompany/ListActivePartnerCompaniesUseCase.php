<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepositoryInterface;

class ListActivePartnerCompaniesUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepositoryInterface $repository
    ) {}

    public function execute()
    {
        return $this->repository->findActive();
    }
} 