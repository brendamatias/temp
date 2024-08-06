<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Application\DTOs\PartnerCompany\CreatePartnerCompanyDTO;
use App\Domain\Entities\PartnerCompany as PartnerCompanyEntity;
use App\Domain\Repositories\PartnerCompanyRepository;

class CreatePartnerCompanyUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(CreatePartnerCompanyDTO $dto): void
    {
        $company = new PartnerCompanyEntity(
            name: $dto->name,
            legalName: $dto->legalName,
            document: $dto->document
        );

        $this->repository->save($company);
    }
} 