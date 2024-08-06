<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\FindInvoiceByNumberDTO;
use App\Application\Validators\Invoice\FindInvoiceByNumberValidator;
use App\Domain\Repositories\InvoiceRepository;

class FindInvoiceByNumberUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly FindInvoiceByNumberValidator $validator
    ) {}

    public function execute(FindInvoiceByNumberDTO $dto)
    {
        $this->validator->validate($dto);
        return $this->invoiceRepository->findByNumber($dto->number);
    }
} 