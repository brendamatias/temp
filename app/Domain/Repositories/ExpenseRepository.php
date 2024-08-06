<?php

namespace App\Domain\Repositories;

interface ExpenseRepository
{
    // ... existing methods ...

    public function getMonthlyExpensesByYear(int $year): array;
} 