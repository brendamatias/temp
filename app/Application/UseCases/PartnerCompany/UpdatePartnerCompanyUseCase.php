<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Application\DTOs\PartnerCompany\UpdatePartnerCompanyDTO;
use App\Domain\Repositories\PartnerCompanyRepository;

class UpdatePartnerCompanyUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(UpdatePartnerCompanyDTO $dto): void
    {
        $company = $this->repository->findById($dto->id);

        if (!$company) {
            throw new \Exception('Empresa parceira não encontrada');
        }

        if ($dto->name) {
            $company->setName($dto->name);
        }

        if ($dto->legalName) {
            $company->setLegalName($dto->legalName);
        }

        if ($dto->document) {
            $company->setDocument($dto->document);
        }

        if (isset($dto->isActive)) {
            $company->setIsActive($dto->isActive);
        }

        $this->repository->save($company);
    }
} 