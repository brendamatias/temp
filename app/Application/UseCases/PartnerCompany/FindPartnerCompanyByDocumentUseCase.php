<?php

namespace App\Application\UseCases\PartnerCompany;

use App\Domain\Repositories\PartnerCompanyRepository;

class FindPartnerCompanyByDocumentUseCase
{
    public function __construct(
        private readonly PartnerCompanyRepository $repository
    ) {}

    public function execute(string $document): ?object
    {
        return $this->repository->findByDocument($document);
    }
} 