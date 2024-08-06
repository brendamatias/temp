<?php

namespace App\Application\UseCases\Invoice;

use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PartnerCompanyRepository;
use App\Application\DTOs\Invoice\UpdateInvoiceDTO;
use App\Application\Validators\Invoice\UpdateInvoiceValidator;

class UpdateInvoiceUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PartnerCompanyRepository $partnerCompanyRepository,
        private readonly UpdateInvoiceValidator $validator
    ) {}

    public function execute(UpdateInvoiceDTO $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $invoice = $this->invoiceRepository->findById($dto->id);

        if ($dto->number !== null) {
            $invoice->setNumber($dto->number);
        }

        if ($dto->value !== null) {
            $invoice->setValue($dto->value);
        }

        if ($dto->serviceDescription !== null) {
            $invoice->setServiceDescription($dto->serviceDescription);
        }

        if ($dto->competenceMonth !== null) {
            $invoice->setCompetenceMonth($dto->competenceMonth);
        }

        if ($dto->receiptDate !== null) {
            $invoice->setReceiptDate($dto->receiptDate);
        }

        if ($dto->partnerCompanyId !== null) {
            $partnerCompany = $this->partnerCompanyRepository->findById($dto->partnerCompanyId);
            $invoice->setPartnerCompany($partnerCompany);
        }

        if ($dto->isActive !== null) {
            $invoice->setIsActive($dto->isActive);
        }

        $this->invoiceRepository->save($invoice);
    }
} 