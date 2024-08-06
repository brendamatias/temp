<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Invoice;

interface InvoiceRepository
{
    public function save(Invoice $invoice): void;
    public function findById(int $id): ?Invoice;
    public function findAll(bool $includeInactive = false): array;
    public function findByPartnerCompany(int $partnerCompanyId): array;
    public function findByNumber(string $number): ?Invoice;
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array;
    public function findByAmountRange(float $minAmount, float $maxAmount): array;
    public function getTotalRevenueByYear(int $year): float;
    public function getMonthlyRevenueByYear(int $year): array;
} 