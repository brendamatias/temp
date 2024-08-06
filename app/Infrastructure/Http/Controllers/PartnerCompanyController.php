<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\PartnerCompany\CreatePartnerCompanyDTO;
use App\Application\DTOs\PartnerCompany\UpdatePartnerCompanyDTO;
use App\Application\DTOs\PartnerCompany\ListPartnerCompaniesDTO;
use App\Application\UseCases\PartnerCompany\CreatePartnerCompanyUseCase;
use App\Application\UseCases\PartnerCompany\UpdatePartnerCompanyUseCase;
use App\Application\UseCases\PartnerCompany\ListPartnerCompaniesUseCase;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByIdUseCase;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByDocumentUseCase;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByNameUseCase;
use App\Application\Validators\PartnerCompany\PartnerCompanyRequestValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;

class PartnerCompanyController extends Controller
{
    public function __construct(
        private readonly CreatePartnerCompanyUseCase $createPartnerCompanyUseCase,
        private readonly UpdatePartnerCompanyUseCase $updatePartnerCompanyUseCase,
        private readonly ListPartnerCompaniesUseCase $listPartnerCompaniesUseCase,
        private readonly FindPartnerCompanyByIdUseCase $findPartnerCompanyByIdUseCase,
        private readonly FindPartnerCompanyByDocumentUseCase $findPartnerCompanyByDocumentUseCase,
        private readonly FindPartnerCompanyByNameUseCase $findPartnerCompanyByNameUseCase,
        private readonly PartnerCompanyRequestValidator $requestValidator
    ) {}

    public function create(Request $request): JsonResponse
    {
        try {
            $errors = $this->requestValidator->validateCreate($request);
            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }

            $dto = new CreatePartnerCompanyDTO(
                name: $request->input('name'),
                legalName: $request->input('legal_name'),
                document: $request->input('document')
            );

            $this->createPartnerCompanyUseCase->execute($dto);

            return response()->json(['message' => 'Empresa parceira criada com sucesso'], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $errors = $this->requestValidator->validateUpdate($request);
            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }

            $dto = new UpdatePartnerCompanyDTO(
                id: $id,
                name: $request->input('name'),
                legalName: $request->input('legal_name'),
                document: $request->input('document'),
                isActive: $request->boolean('is_active')
            );

            $this->updatePartnerCompanyUseCase->execute($dto);

            return response()->json(['message' => 'Empresa parceira atualizada com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $dto = new ListPartnerCompaniesDTO(
                page: $request->integer('page', 1),
                perPage: $request->integer('per_page', 10)
            );

            $partnerCompanies = $this->listPartnerCompaniesUseCase->execute($dto);

            return response()->json(['data' => array_map(fn($company) => $company->toArray(), $partnerCompanies)]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function findById(string $id): JsonResponse
    {
        try {
            $company = $this->findPartnerCompanyByIdUseCase->execute((int) $id);

            if (!$company) {
                return response()->json(['message' => 'Empresa parceira não encontrada'], 404);
            }

            return response()->json(['data' => $company->toArray()]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function findByDocument(string $document): JsonResponse
    {
        try {
            $company = $this->findPartnerCompanyByDocumentUseCase->execute($document);

            if (!$company) {
                return response()->json(['message' => 'Empresa parceira não encontrada'], 404);
            }
            
            return response()->json(['data' => $company->toArray()]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->query('q');
            if (!$query) {
                return response()->json([]);
            }
            
            $companies = $this->findPartnerCompanyByNameUseCase->execute($query);
            return response()->json($companies);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }
} 