<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByPartnerCompanyDTO;
use App\Application\Validators\Invoice\FindInvoicesByPartnerCompanyValidator;
use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PartnerCompanyRepository;

class FindInvoicesByPartnerCompanyUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PartnerCompanyRepository $partnerCompanyRepository,
        private readonly FindInvoicesByPartnerCompanyValidator $validator
    ) {}

    public function execute(FindInvoicesByPartnerCompanyDTO $dto)
    {
        $this->validator->validate($dto);
        
        $partnerCompany = $this->partnerCompanyRepository->findById($dto->partnerCompanyId);
        if (!$partnerCompany) {
            throw new \InvalidArgumentException('Empresa parceira não encontrada');
        }

        return $this->invoiceRepository->findByPartnerCompany($dto->partnerCompanyId);
    }
} 