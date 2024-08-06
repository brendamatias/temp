<?php

namespace App\Application\DTOs\Invoice;

use App\Domain\Entities\Invoice;

class InvoiceResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $number,
        public readonly float $value,
        public readonly string $serviceDescription,
        public readonly string $competenceMonth,
        public readonly string $receiptDate,
        public readonly array $partnerCompany,
        public readonly bool $isActive,
        public readonly string $createdAt,
        public readonly ?string $updatedAt
    ) {}

    public static function fromEntity(Invoice $invoice): self
    {
        return new self(
            id: $invoice->getId(),
            number: $invoice->getNumber(),
            value: $invoice->getValue(),
            serviceDescription: $invoice->getServiceDescription(),
            competenceMonth: $invoice->getCompetenceMonth()->format('Y-m-d'),
            receiptDate: $invoice->getReceiptDate()->format('Y-m-d'),
            partnerCompany: [
                'id' => $invoice->getPartnerCompany()->getId(),
                'name' => $invoice->getPartnerCompany()->getName()
            ],
            isActive: $invoice->isActive(),
            createdAt: $invoice->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $invoice->getUpdatedAt()?->format('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'value' => $this->value,
            'service_description' => $this->serviceDescription,
            'competence_month' => $this->competenceMonth,
            'receipt_date' => $this->receiptDate,
            'partner_company' => $this->partnerCompany,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
} 