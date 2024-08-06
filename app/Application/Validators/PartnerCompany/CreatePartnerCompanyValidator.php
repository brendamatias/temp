<?php

namespace App\Application\Validators\PartnerCompany;

use App\Application\DTOs\PartnerCompany\CreatePartnerCompanyDTO;
use InvalidArgumentException;

class CreatePartnerCompanyValidator
{
    public function validate(CreatePartnerCompanyDTO $dto): void
    {
        if (empty($dto->name)) {
            throw new InvalidArgumentException('O nome da empresa é obrigatório');
        }

        if (strlen($dto->name) > 100) {
            throw new InvalidArgumentException('O nome da empresa não pode ter mais de 100 caracteres');
        }

        if (empty($dto->legalName)) {
            throw new InvalidArgumentException('A razão social é obrigatória');
        }

        if (strlen($dto->legalName) > 100) {
            throw new InvalidArgumentException('A razão social não pode ter mais de 100 caracteres');
        }

        if (empty($dto->cnpj)) {
            throw new InvalidArgumentException('O CNPJ é obrigatório');
        }

        if (!preg_match('/^\d{14}$/', preg_replace('/[^0-9]/', '', $dto->cnpj))) {
            throw new InvalidArgumentException('CNPJ inválido');
        }

        if (empty($dto->email)) {
            throw new InvalidArgumentException('O email é obrigatório');
        }

        if (!filter_var($dto->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email inválido');
        }

        if (empty($dto->phone)) {
            throw new InvalidArgumentException('O telefone é obrigatório');
        }

        if (strlen(preg_replace('/[^0-9]/', '', $dto->phone)) < 10) {
            throw new InvalidArgumentException('Telefone inválido');
        }

        if (empty($dto->address)) {
            throw new InvalidArgumentException('O endereço é obrigatório');
        }

        if (strlen($dto->address) > 200) {
            throw new InvalidArgumentException('O endereço não pode ter mais de 200 caracteres');
        }

        if (empty($dto->city)) {
            throw new InvalidArgumentException('A cidade é obrigatória');
        }

        if (strlen($dto->city) > 100) {
            throw new InvalidArgumentException('A cidade não pode ter mais de 100 caracteres');
        }

        if (empty($dto->state)) {
            throw new InvalidArgumentException('O estado é obrigatório');
        }

        if (strlen($dto->state) != 2) {
            throw new InvalidArgumentException('O estado deve ter 2 caracteres');
        }

        if (empty($dto->zipCode)) {
            throw new InvalidArgumentException('O CEP é obrigatório');
        }

        if (!preg_match('/^\d{8}$/', preg_replace('/[^0-9]/', '', $dto->zipCode))) {
            throw new InvalidArgumentException('CEP inválido');
        }

        if (strlen($dto->country) > 100) {
            throw new InvalidArgumentException('O país não pode ter mais de 100 caracteres');
        }
    }
} 