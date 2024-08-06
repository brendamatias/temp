<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\ExpenseCategory;

interface ExpenseCategoryRepository
{
    public function save(ExpenseCategory $category): void;
    public function findById(int $id): ?ExpenseCategory;
    public function findAll(bool $includeArchived = false): array;
    public function findByName(string $name): array;
    public function archive(int $id): void;
    public function unarchive(int $id): void;
    public function getExpensesByCategoryAndYear(int $year): array;
} 