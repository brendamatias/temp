<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Invoice;
use App\Domain\Entities\PartnerCompany;
use App\Domain\Repositories\InvoiceRepository as InvoiceRepositoryInterface;
use App\Infrastructure\Database\Models\Invoice as InvoiceModel;
use Illuminate\Support\Collection;
use App\Domain\Entities\InvoiceEntity;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly PartnerCompanyRepository $companyRepository
    ) {}

    public function save(Invoice $invoice): void
    {
        $model = new InvoiceModel();
        $model->number = $invoice->getNumber();
        $model->value = $invoice->getValue();
        $model->service_description = $invoice->getServiceDescription();
        $model->competence_month = $invoice->getCompetenceMonth()->format('Y-m-d');
        $model->receipt_date = $invoice->getReceiptDate()->format('Y-m-d');
        $model->partner_company_id = $invoice->getPartnerCompany()->getId();
        $model->is_active = true;
        $model->save();
    }

    public function findById(int $id): ?Invoice
    {
        $model = InvoiceModel::find($id);
        
        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findAll(bool $includeInactive = false): array
    {
        $query = InvoiceModel::query();
        
        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        return $query->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function findByPartnerCompany(int $partnerCompanyId): array
    {
        return InvoiceModel::where('partner_company_id', $partnerCompanyId)
            ->where('is_active', true)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function findByNumber(string $number): ?Invoice
    {
        $model = InvoiceModel::where('number', $number)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->toEntity($model);
    }

    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return InvoiceModel::whereBetween('receipt_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->where('is_active', true)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function findByAmountRange(float $minAmount, float $maxAmount): array
    {
        return InvoiceModel::whereBetween('value', [$minAmount, $maxAmount])
            ->where('is_active', true)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function getTotalRevenueByYear(int $year): float
    {
        return InvoiceModel::whereYear('receipt_date', $year)
            ->where('is_active', true)
            ->sum('value');
    }

    public function getMonthlyRevenueByYear(int $year): array
    {
        return InvoiceModel::selectRaw('EXTRACT(MONTH FROM receipt_date) as month, SUM(value) as value')
            ->whereYear('receipt_date', $year)
            ->where('is_active', true)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('F', mktime(0, 0, 0, (int)$item->month, 1)),
                    'value' => $item->value
                ];
            })
            ->toArray();
    }

    private function toEntity(InvoiceModel $model): Invoice
    {
        $partnerCompany = $this->companyRepository->findById($model->partner_company_id);
        
        $invoice = new Invoice(
            $model->number,
            $model->value,
            $model->service_description,
            new \DateTime($model->competence_month),
            new \DateTime($model->receipt_date),
            $partnerCompany
        );
        
        $invoice->setId($model->id);
        $invoice->setIsActive($model->is_active);
        
        return $invoice;
    }
} 