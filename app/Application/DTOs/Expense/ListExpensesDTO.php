<?php

namespace App\Application\DTOs\Expense;

class ListExpensesDTO
{
    public function __construct(
        public readonly bool $includeInactive = false,
        public readonly ?string $search = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $partnerCompanyId = null,
        public readonly ?\DateTime $startDate = null,
        public readonly ?\DateTime $endDate = null,
        public readonly ?\DateTime $startCompetenceDate = null,
        public readonly ?\DateTime $endCompetenceDate = null,
        public readonly int $page = 1,
        public readonly int $perPage = 10
    ) {}
} 