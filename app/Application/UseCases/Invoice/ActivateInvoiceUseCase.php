<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\ActivateInvoiceDTO;
use App\Application\Validators\Invoice\ActivateInvoiceValidator;
use App\Domain\Repositories\InvoiceRepository;

class ActivateInvoiceUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly ActivateInvoiceValidator $validator
    ) {}

    public function execute(ActivateInvoiceDTO $dto): void
    {
        $this->validator->validate($dto);
        
        $invoice = $this->invoiceRepository->findById($dto->id);
        if (!$invoice) {
            throw new \InvalidArgumentException('Nota fiscal não encontrada');
        }

        if ($invoice->isActive()) {
            throw new \InvalidArgumentException('Nota fiscal já está ativa');
        }

        $invoice->setIsActive(true);
        $this->invoiceRepository->save($invoice);
    }
} 