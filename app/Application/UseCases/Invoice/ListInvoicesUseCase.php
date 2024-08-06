<?php

namespace App\Application\UseCases\Invoice;

use App\Domain\Repositories\InvoiceRepository;
use App\Application\DTOs\Invoice\ListInvoicesDTO;
use App\Application\DTOs\Invoice\InvoiceResponseDTO;

class ListInvoicesUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository
    ) {}

    public function execute(ListInvoicesDTO $dto): array
    {
        $invoices = $this->invoiceRepository->findAll($dto->includeInactive);
        
        return array_map(
            fn($invoice) => InvoiceResponseDTO::fromEntity($invoice)->toArray(),
            $invoices
        );
    }
} 