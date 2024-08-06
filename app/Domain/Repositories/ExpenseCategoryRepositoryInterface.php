<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ExpenseCategory;

interface ExpenseCategoryRepositoryInterface
{
    public function all(bool $includeInactive = false): array;
    public function findById(int $id, bool $includeInactive = false): ?ExpenseCategory;
    public function findByName(string $name, bool $includeInactive = false): array;
    public function findActive(): array;
    public function findInactive(): array;
    public function create(ExpenseCategory $category): ExpenseCategory;
    public function update(ExpenseCategory $category): ExpenseCategory;
    public function delete(int $id): bool;
    public function activate(int $id): bool;
    public function deactivate(int $id): bool;
} 