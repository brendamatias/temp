<?php

namespace App\Domain\Repositories;

interface PartnerCompanyRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): ?object;
    public function findByName(string $name): array;
    public function findByLegalName(string $legalName): ?object;
    public function findAllWithInvoices(): array;
    public function findAllWithExpenses(): array;
    public function findAllActive(): array;
    public function findAllInactive(): array;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
} 