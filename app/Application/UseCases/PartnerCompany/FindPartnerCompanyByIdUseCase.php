<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepository;

class FindPartnerCompanyByIdUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(int $id): ?object
    {
        return $this->repository->findById($id);
    }
} 