<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\PartnerCompany as PartnerCompanyEntity;
use App\Domain\Repositories\PartnerCompanyRepository as PartnerCompanyRepositoryInterface;
use App\Infrastructure\Database\Models\PartnerCompany as PartnerCompanyModel;
use App\Domain\ValueObjects\Document;

class PartnerCompanyRepository implements PartnerCompanyRepositoryInterface
{
    public function save(PartnerCompanyEntity $company): void
    {
        $model = $company->getId()
            ? PartnerCompanyModel::find($company->getId())
            : new PartnerCompanyModel();

        $model->name = $company->getName();
        $model->legal_name = $company->getLegalName();
        $model->document = $company->getDocument();
        $model->is_active = $company->isActive();
        $model->save();

        if (!$company->getId()) {
            $company->setId($model->id);
        }
    }

    public function findById(int $id): ?PartnerCompanyEntity
    {
        $model = PartnerCompanyModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(int $page = 1, int $perPage = 10, ?bool $includeInactive = null): array
    {
        $query = PartnerCompanyModel::query();
        if ($includeInactive === false) {
            $query->where('is_active', true);
        } elseif ($includeInactive === true) {
            $query->where('is_active', false);
        }
        $models = $query->paginate($perPage, ['*'], 'page', $page);
        return array_map(fn($model) => $this->toEntity($model), $models->items());
    }

    public function findByDocument(string $document): ?PartnerCompanyEntity
    {
        $document = new Document($document);
        \Log::info('Buscando empresa com documento: ' . $document->getValue());
        
        $model = PartnerCompanyModel::where('document', $document->getValue())->first();
        
        \Log::info('Resultado da busca: ' . ($model ? 'encontrado' : 'não encontrado'));
        
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $name): array
    {
        $models = PartnerCompanyModel::where('name', 'like', "%$name%")
            ->orWhere('legal_name', 'like', "%$name%")
            ->get();
        return $models->map(fn($model) => $this->toEntity($model))->all();
    }

    private function toEntity(PartnerCompanyModel $model): PartnerCompanyEntity
    {
        $entity = new PartnerCompanyEntity(
            id: $model->id,
            name: $model->name,
            legalName: $model->legal_name,
            document: $model->document
        );
        $entity->setIsActive($model->is_active);
        return $entity;
    }
} 