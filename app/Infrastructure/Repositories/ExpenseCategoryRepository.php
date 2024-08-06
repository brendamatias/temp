<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\ExpenseCategory as ExpenseCategoryEntity;
use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use App\Infrastructure\Database\Models\ExpenseCategory as ExpenseCategoryModel;

class ExpenseCategoryRepository implements ExpenseCategoryRepositoryInterface
{
    public function all(bool $includeInactive = false): array
    {
        $query = ExpenseCategoryModel::query();
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findById(int $id, bool $includeInactive = false): ?ExpenseCategoryEntity
    {
        $query = ExpenseCategoryModel::query()->where('id', $id);
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        $model = $query->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $name, bool $includeInactive = false): array
    {
        $query = ExpenseCategoryModel::query()->where('name', 'like', "%$name%");
        if (!$includeInactive) {
            $query->where('is_active', true);
        }
        return $query->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findActive(): array
    {
        return ExpenseCategoryModel::where('is_active', true)->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function findInactive(): array
    {
        return ExpenseCategoryModel::where('is_active', false)->get()->map(fn($model) => $this->toEntity($model))->all();
    }

    public function create(ExpenseCategoryEntity $category): ExpenseCategoryEntity
    {
        $model = new ExpenseCategoryModel();
        $model->name = $category->getName();
        $model->description = $category->getDescription();
        $model->is_active = $category->isActive();
        $model->save();
        $category->setId($model->id);
        return $category;
    }

    public function update(ExpenseCategoryEntity $category): ExpenseCategoryEntity
    {
        $model = ExpenseCategoryModel::find($category->getId());
        if (!$model) {
            throw new \InvalidArgumentException('Categoria não encontrada');
        }
        $model->name = $category->getName();
        $model->description = $category->getDescription();
        $model->is_active = $category->isActive();
        $model->save();
        return $category;
    }

    public function delete(int $id): bool
    {
        $model = ExpenseCategoryModel::find($id);
        if (!$model) {
            return false;
        }
        return $model->delete();
    }

    public function activate(int $id): bool
    {
        $model = ExpenseCategoryModel::find($id);
        if (!$model) {
            return false;
        }
        $model->is_active = true;
        $model->save();
        return true;
    }

    public function deactivate(int $id): bool
    {
        $model = ExpenseCategoryModel::find($id);
        if (!$model) {
            return false;
        }
        $model->is_active = false;
        $model->save();
        return true;
    }

    public function getExpensesByCategoryAndYear(int $year): array
    {
        return ExpenseCategoryModel::join('expenses', 'expense_categories.id', '=', 'expenses.category_id')
            ->whereYear('expenses.date', $year)
            ->selectRaw('expense_categories.name as category, SUM(expenses.value) as total')
            ->groupBy('expense_categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->name,
                    'value' => $item->total
                ];
            })
            ->toArray();
    }

    private function toEntity(ExpenseCategoryModel $model): ExpenseCategoryEntity
    {
        $entity = new ExpenseCategoryEntity(
            $model->name,
            $model->description
        );
        $entity->setId($model->id);
        $entity->setIsActive($model->is_active);
        return $entity;
    }
} 