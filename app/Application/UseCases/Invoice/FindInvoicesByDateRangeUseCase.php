<?php

namespace App\Application\UseCases\Invoice;

use App\Application\DTOs\Invoice\FindInvoicesByDateRangeDTO;
use App\Application\Validators\Invoice\FindInvoicesByDateRangeValidator;
use App\Domain\Repositories\InvoiceRepository;

class FindInvoicesByDateRangeUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly FindInvoicesByDateRangeValidator $validator
    ) {}

    public function execute(FindInvoicesByDateRangeDTO $dto)
    {
        $this->validator->validate($dto);
        return $this->invoiceRepository->findByDateRange($dto->startDate, $dto->endDate);
    }
} 