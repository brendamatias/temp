<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\ExpenseCategory\CreateExpenseCategoryDTO;
use App\Application\DTOs\ExpenseCategory\UpdateExpenseCategoryDTO;
use App\Application\DTOs\ExpenseCategory\FindExpenseCategoryByIdDTO;
use App\Application\UseCases\ExpenseCategory\CreateExpenseCategoryUseCase;
use App\Application\UseCases\ExpenseCategory\UpdateExpenseCategoryUseCase;
use App\Application\UseCases\ExpenseCategory\ListExpenseCategoriesUseCase;
use App\Application\UseCases\ExpenseCategory\FindExpenseCategoryByIdUseCase;
use App\Application\UseCases\ExpenseCategory\ActivateExpenseCategoryUseCase;
use App\Application\UseCases\ExpenseCategory\DeactivateExpenseCategoryUseCase;
use App\Infrastructure\Http\Requests\ExpenseCategory\CreateExpenseCategoryRequest;
use App\Infrastructure\Http\Requests\ExpenseCategory\UpdateExpenseCategoryRequest;
use Illuminate\Http\JsonResponse;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        private readonly CreateExpenseCategoryUseCase $createExpenseCategoryUseCase,
        private readonly UpdateExpenseCategoryUseCase $updateExpenseCategoryUseCase,
        private readonly ListExpenseCategoriesUseCase $listExpenseCategoriesUseCase,
        private readonly FindExpenseCategoryByIdUseCase $findExpenseCategoryByIdUseCase,
        private readonly ActivateExpenseCategoryUseCase $activateExpenseCategoryUseCase,
        private readonly DeactivateExpenseCategoryUseCase $deactivateExpenseCategoryUseCase
    ) {}

    public function create(CreateExpenseCategoryRequest $request): JsonResponse
    {
        $dto = new CreateExpenseCategoryDTO(
            name: $request->input('name'),
            description: $request->input('description')
        );
        
        $category = $this->createExpenseCategoryUseCase->execute($dto);
        
        return response()->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'is_active' => $category->isActive(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $category->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ], 201);
    }

    public function update(int $id, UpdateExpenseCategoryRequest $request): JsonResponse
    {
        $dto = new UpdateExpenseCategoryDTO(
            id: $id,
            name: $request->input('name'),
            description: $request->input('description')
        );
        
        $category = $this->updateExpenseCategoryUseCase->execute($dto);
        
        return response()->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'is_active' => $category->isActive(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $category->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ]);
    }

    public function list(): JsonResponse
    {
        $categories = $this->listExpenseCategoriesUseCase->execute();
        
        $response = array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'is_active' => $category->isActive(),
                'created_at' => $category->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $category->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
            ];
        }, $categories);
        
        return response()->json($response);
    }

    public function findById(int $id): JsonResponse
    {
        $dto = new FindExpenseCategoryByIdDTO($id);
        $category = $this->findExpenseCategoryByIdUseCase->execute($dto);
        
        if (!$category) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }
        
        return response()->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'is_active' => $category->isActive(),
            'created_at' => $category->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $category->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
        ]);
    }

    public function activate(int $id): JsonResponse
    {
        $this->activateExpenseCategoryUseCase->execute($id);
        return response()->json(['message' => 'Categoria ativada com sucesso']);
    }

    public function deactivate(int $id): JsonResponse
    {
        $this->deactivateExpenseCategoryUseCase->execute($id);
        return response()->json(['message' => 'Categoria desativada com sucesso']);
    }
} 