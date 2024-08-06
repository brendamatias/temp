<?php

namespace App\Application\Validators\Invoice;

use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\PartnerCompanyRepository;
use App\Application\DTOs\Invoice\CreateInvoiceDTO;

class CreateInvoiceValidator
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly PartnerCompanyRepository $partnerCompanyRepository
    ) {}

    public function validate(CreateInvoiceDTO $dto): array
    {
        $errors = [];

        if (empty($dto->number)) {
            $errors['number'][] = 'O número da nota fiscal é obrigatório';
        }

        if ($dto->value <= 0) {
            $errors['value'][] = 'O valor deve ser maior que zero';
        }

        if (empty($dto->serviceDescription)) {
            $errors['serviceDescription'][] = 'A descrição do serviço é obrigatória';
        }

        if (!$dto->competenceMonth instanceof \DateTime) {
            $errors['competenceMonth'][] = 'O mês de competência é inválido';
        }

        if (!$dto->receiptDate instanceof \DateTime) {
            $errors['receiptDate'][] = 'A data de recebimento é inválida';
        }

        $partnerCompany = $this->partnerCompanyRepository->findById($dto->partnerCompanyId);
        if (!$partnerCompany) {
            $errors['partnerCompanyId'][] = 'Empresa parceira não encontrada';
        }

        $existingInvoice = $this->invoiceRepository->findByNumber($dto->number);
        if ($existingInvoice) {
            $errors['number'][] = 'Já existe uma nota fiscal com este número';
        }

        return $errors;
    }
} 