<?php

namespace App\Application\DTOs\ExpenseCategory;

class ListExpenseCategoriesDTO
{
    public function __construct(
        public readonly bool $includeInactive = false,
        public readonly ?string $search = null,
        public readonly int $page = 1,
        public readonly int $perPage = 10
    ) {}
} 