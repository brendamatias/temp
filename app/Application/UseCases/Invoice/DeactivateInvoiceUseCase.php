<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\DeactivateInvoiceDTO;
use App\Application\Validators\Invoice\DeactivateInvoiceValidator;
use App\Domain\Repositories\InvoiceRepository;

class DeactivateInvoiceUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly DeactivateInvoiceValidator $validator
    ) {}

    public function execute(DeactivateInvoiceDTO $dto): void
    {
        $this->validator->validate($dto);
        
        $invoice = $this->invoiceRepository->findById($dto->id);
        if (!$invoice) {
            throw new \InvalidArgumentException('Nota fiscal não encontrada');
        }

        if (!$invoice->isActive()) {
            throw new \InvalidArgumentException('Nota fiscal já está inativa');
        }

        $invoice->setIsActive(false);
        $this->invoiceRepository->save($invoice);
    }
} 