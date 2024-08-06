<?php

namespace App\Application\Validators\Invoice;

use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PartnerCompanyRepository;
use App\Application\DTOs\Invoice\UpdateInvoiceDTO;

class UpdateInvoiceValidator
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PartnerCompanyRepository $partnerCompanyRepository
    ) {}

    public function validate(UpdateInvoiceDTO $dto): array
    {
        $errors = [];

        $invoice = $this->invoiceRepository->findById($dto->id);
        if (!$invoice) {
            $errors['id'][] = 'Nota fiscal não encontrada';
            return $errors;
        }

        if ($dto->number !== null && empty($dto->number)) {
            $errors['number'][] = 'O número da nota fiscal é obrigatório';
        }

        if ($dto->value !== null && $dto->value <= 0) {
            $errors['value'][] = 'O valor deve ser maior que zero';
        }

        if ($dto->serviceDescription !== null && empty($dto->serviceDescription)) {
            $errors['serviceDescription'][] = 'A descrição do serviço é obrigatória';
        }

        if ($dto->competenceMonth !== null && !$dto->competenceMonth instanceof \DateTime) {
            $errors['competenceMonth'][] = 'O mês de competência é inválido';
        }

        if ($dto->receiptDate !== null && !$dto->receiptDate instanceof \DateTime) {
            $errors['receiptDate'][] = 'A data de recebimento é inválida';
        }

        if ($dto->partnerCompanyId !== null) {
            $partnerCompany = $this->partnerCompanyRepository->findById($dto->partnerCompanyId);
            if (!$partnerCompany) {
                $errors['partnerCompanyId'][] = 'Empresa parceira não encontrada';
            }
        }

        if ($dto->number !== null) {
            $existingInvoice = $this->invoiceRepository->findByNumber($dto->number);
            if ($existingInvoice && $existingInvoice->getId() !== $dto->id) {
                $errors['number'][] = 'Já existe uma nota fiscal com este número';
            }
        }

        return $errors;
    }
} 