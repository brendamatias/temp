<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function all(bool $includeInactive = false): array;
    public function findById(int $id, bool $includeInactive = false): ?Expense;
    public function findByName(string $name, bool $includeInactive = false): array;
    public function findByCategory(int $categoryId, bool $includeInactive = false): array;
    public function findByPartnerCompany(int $partnerCompanyId, bool $includeInactive = false): array;
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false): array;
    public function findByCompetenceDateRange(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false): array;
    public function findActive(): array;
    public function findInactive(): array;
    public function create(Expense $expense): Expense;
    public function update(Expense $expense): Expense;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
} 