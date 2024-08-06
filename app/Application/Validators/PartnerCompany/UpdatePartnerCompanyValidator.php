<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\UpdatePartnerCompanyDTO;
use InvalidArgumentException;

class UpdatePartnerCompanyValidator
{
    public function validate(UpdatePartnerCompanyDTO $dto): void
    {
        if ($dto->id <= 0) {
            throw new InvalidArgumentException('O ID da empresa deve ser maior que 0');
        }

        if ($dto->name !== null) {
            if (empty($dto->name)) {
                throw new InvalidArgumentException('O nome da empresa não pode ser vazio');
            }

            if (strlen($dto->name) > 100) {
                throw new InvalidArgumentException('O nome da empresa não pode ter mais de 100 caracteres');
            }
        }

        if ($dto->legalName !== null) {
            if (empty($dto->legalName)) {
                throw new InvalidArgumentException('A razão social não pode ser vazia');
            }

            if (strlen($dto->legalName) > 100) {
                throw new InvalidArgumentException('A razão social não pode ter mais de 100 caracteres');
            }
        }

        if ($dto->cnpj !== null) {
            if (empty($dto->cnpj)) {
                throw new InvalidArgumentException('O CNPJ não pode ser vazio');
            }

            if (!preg_match('/^\d{14}$/', preg_replace('/[^0-9]/', '', $dto->cnpj))) {
                throw new InvalidArgumentException('CNPJ inválido');
            }
        }

        if ($dto->email !== null) {
            if (empty($dto->email)) {
                throw new InvalidArgumentException('O email não pode ser vazio');
            }

            if (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Email inválido');
            }
        }

        if ($dto->phone !== null) {
            if (empty($dto->phone)) {
                throw new InvalidArgumentException('O telefone não pode ser vazio');
            }

            if (strlen(preg_replace('/[^0-9]/', '', $dto->phone)) < 10) {
                throw new InvalidArgumentException('Telefone inválido');
            }
        }

        if ($dto->address !== null) {
            if (empty($dto->address)) {
                throw new InvalidArgumentException('O endereço não pode ser vazio');
            }

            if (strlen($dto->address) > 200) {
                throw new InvalidArgumentException('O endereço não pode ter mais de 200 caracteres');
            }
        }

        if ($dto->city !== null) {
            if (empty($dto->city)) {
                throw new InvalidArgumentException('A cidade não pode ser vazia');
            }

            if (strlen($dto->city) > 100) {
                throw new InvalidArgumentException('A cidade não pode ter mais de 100 caracteres');
            }
        }

        if ($dto->state !== null) {
            if (empty($dto->state)) {
                throw new InvalidArgumentException('O estado não pode ser vazio');
            }

            if (strlen($dto->state) != 2) {
                throw new InvalidArgumentException('O estado deve ter 2 caracteres');
            }
        }

        if ($dto->zipCode !== null) {
            if (empty($dto->zipCode)) {
                throw new InvalidArgumentException('O CEP não pode ser vazio');
            }

            if (!preg_match('/^\d{8}$/', preg_replace('/[^0-9]/', '', $dto->zipCode))) {
                throw new InvalidArgumentException('CEP inválido');
            }
        }

        if ($dto->country !== null && strlen($dto->country) > 100) {
            throw new InvalidArgumentException('O país não pode ter mais de 100 caracteres');
        }
    }
} 