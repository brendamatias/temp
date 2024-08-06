<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\FindInvoiceByIdDTO;
use App\Application\Validators\Invoice\FindInvoiceByIdValidator;
use App\Domain\Repositories\InvoiceRepository;

class FindInvoiceByIdUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly FindInvoiceByIdValidator $validator
    ) {}

    public function execute(FindInvoiceByIdDTO $dto)
    {
        $this->validator->validate($dto);
        return $this->invoiceRepository->findById($dto->id);
    }
} 