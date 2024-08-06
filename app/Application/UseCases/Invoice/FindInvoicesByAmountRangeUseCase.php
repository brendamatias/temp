<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByAmountRangeDTO;
use App\Application\Validators\Invoice\FindInvoicesByAmountRangeValidator;
use App\Domain\Repositories\InvoiceRepository;

class FindInvoicesByAmountRangeUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly FindInvoicesByAmountRangeValidator $validator
    ) {}

    public function execute(FindInvoicesByAmountRangeDTO $dto)
    {
        $this->validator->validate($dto);
        return $this->invoiceRepository->findByAmountRange($dto->minAmount, $dto->maxAmount);
    }
} 