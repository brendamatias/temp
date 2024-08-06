<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepositoryInterface;

class ActivatePartnerCompanyUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        return $this->repository->activate($id);
    }
} 