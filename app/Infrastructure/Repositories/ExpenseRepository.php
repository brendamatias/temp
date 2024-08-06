<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Expense as ExpenseEntity;
use App\Domain\Entities\ExpenseCategory;
use App\Domain\Entities\PartnerCompany;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Infrastructure\Database\Models\Expense;
use App\Infrastructure\Database\Models\Expense as ExpenseModel;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    private Collection $expenses;
    private ExpenseCategoryRepository $categoryRepository;
    private PartnerCompanyRepository $companyRepository;

    public function __construct(
        ExpenseCategoryRepository $categoryRepository,
        PartnerCompanyRepository $companyRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->companyRepository = $companyRepository;
        $this->expenses = collect();
    }

    public function all(bool $includeInactive = false): array
    {
        $query = Expense::query();
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findById(int $id, bool $includeInactive = false): ?ExpenseEntity
    {
        $query = Expense::query()->where('id', $id);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        $model = $query->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $name, bool $includeInactive = false): array
    {
        $query = Expense::query()->where('name', 'like', "%{$name}%");
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findByCategory(int $categoryId, bool $includeInactive = false): array
    {
        $query = Expense::query()->where('category_id', $categoryId);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findByPartnerCompany(int $partnerCompanyId, bool $includeInactive = false): array
    {
        $query = Expense::query()->where('partner_company_id', $partnerCompanyId);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findByDateRange(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false): array
    {
        $query = Expense::query()
            ->whereBetween('payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findByCompetenceDateRange(\DateTime $startDate, \DateTime $endDate, bool $includeInactive = false): array
    {
        $query = Expense::query()
            ->whereBetween('competence_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findActive(): array
    {
        return Expense::where('is_active', true)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function findInactive(): array
    {
        return Expense::where('is_active', false)
            ->get()
            ->map(fn($model) => $this->toEntity($model))
            ->all();
    }

    public function create(ExpenseEntity $expense): ExpenseEntity
    {
        $model = new Expense([
            'name' => $expense->getName(),
            'value' => $expense->getValue(),
            'payment_date' => $expense->getPaymentDate()->format('Y-m-d'),
            'competence_date' => $expense->getCompetenceDate()->format('Y-m-d'),
            'category_id' => $expense->getCategoryId(),
            'partner_company_id' => $expense->getPartnerCompanyId(),
            'invoice_id' => $expense->getInvoiceId(),
            'is_active' => $expense->isActive(),
            'is_paid' => $expense->isPaid()
        ]);

        $model->save();
        $expense->setId($model->id);

        return $expense;
    }

    public function update(ExpenseEntity $expense): ExpenseEntity
    {
        $model = Expense::findOrFail($expense->getId());
        
        $model->update([
            'name' => $expense->getName(),
            'value' => $expense->getValue(),
            'payment_date' => $expense->getPaymentDate()->format('Y-m-d'),
            'competence_date' => $expense->getCompetenceDate()->format('Y-m-d'),
            'category_id' => $expense->getCategoryId(),
            'partner_company_id' => $expense->getPartnerCompanyId(),
            'invoice_id' => $expense->getInvoiceId(),
            'is_active' => $expense->isActive(),
            'is_paid' => $expense->isPaid()
        ]);

        return $expense;
    }

    public function delete(int $id): bool
    {
        return Expense::destroy($id) > 0;
    }

    public function activate(int $id): bool
    {
        $model = Expense::findOrFail($id);
        return $model->update(['is_active' => true]);
    }

    public function deactivate(int $id): bool
    {
        $model = Expense::findOrFail($id);
        return $model->update(['is_active' => false]);
    }

    public function getMonthlyExpensesByYear(int $year): array
    {
        return ExpenseModel::selectRaw('EXTRACT(MONTH FROM competence_date) as month, SUM(value) as value')
            ->whereYear('competence_date', $year)
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

    public function getExpensesByCategory(int $year): array
    {
        return ExpenseModel::selectRaw('category_id, SUM(value) as value')
            ->whereYear('competence_date', $year)
            ->where('is_active', true)
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name,
                    'value' => $item->value
                ];
            })
            ->toArray();
    }

    private function toEntity(Expense $model): ExpenseEntity
    {
        $expense = new ExpenseEntity(
            name: $model->name,
            value: $model->value,
            categoryId: $model->category_id,
            paymentDate: new \DateTime($model->payment_date),
            competenceDate: new \DateTime($model->competence_date),
            partnerCompanyId: $model->partner_company_id,
            invoiceId: $model->invoice_id,
            isPaid: $model->is_paid
        );

        $expense->setId($model->id);
        
        if (!$model->is_active) {
            $expense->deactivate();
        }

        return $expense;
    }
} 