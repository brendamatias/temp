<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Application\DTOs\PartnerCompany\ListPartnerCompaniesDTO;
use App\Domain\Repositories\PartnerCompanyRepository;

class ListPartnerCompaniesUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(ListPartnerCompaniesDTO $dto): array
    {
        return $this->repository->findAll(
            page: $dto->page,
            perPage: $dto->perPage
        );
    }
} 