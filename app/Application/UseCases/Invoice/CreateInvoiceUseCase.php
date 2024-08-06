<?php

namespace App\Application\UseCases\Invoice;

use App\Domain\Entities\Invoice;
use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PartnerCompanyRepository;
use App\Application\DTOs\Invoice\CreateInvoiceDTO;
use App\Application\Validators\Invoice\CreateInvoiceValidator;

class CreateInvoiceUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PartnerCompanyRepository $partnerCompanyRepository,
        private readonly CreateInvoiceValidator $validator
    ) {}

    public function execute(CreateInvoiceDTO $dto): Invoice
    {
        $errors = $this->validator->validate($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $partnerCompany = $this->partnerCompanyRepository->findById($dto->partnerCompanyId);
        
        $invoice = new Invoice(
            $dto->number,
            $dto->value,
            $dto->serviceDescription,
            $dto->competenceMonth,
            $dto->receiptDate,
            $partnerCompany
        );

        $this->invoiceRepository->save($invoice);

        return $invoice;
    }
} 