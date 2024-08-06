<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\Expense\CreateExpenseDTO;
use App\Application\DTOs\Expense\UpdateExpenseDTO;
use App\Application\DTOs\Expense\FindExpenseByIdDTO;
use App\Application\UseCases\Expense\CreateExpenseUseCase;
use App\Application\UseCases\Expense\UpdateExpenseUseCase;
use App\Application\UseCases\Expense\ListExpensesUseCase;
use App\Application\UseCases\Expense\FindExpenseByIdUseCase;
use App\Application\UseCases\Expense\ActivateExpenseUseCase;
use App\Application\UseCases\Expense\DeactivateExpenseUseCase;
use App\Application\UseCases\Expense\DeleteExpenseUseCase;
use App\Infrastructure\Http\Requests\Expense\CreateExpenseRequest;
use App\Infrastructure\Http\Requests\Expense\UpdateExpenseRequest;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly CreateExpenseUseCase $createExpenseUseCase,
        private readonly UpdateExpenseUseCase $updateExpenseUseCase,
        private readonly ListExpensesUseCase $listExpensesUseCase,
        private readonly FindExpenseByIdUseCase $findExpenseByIdUseCase,
        private readonly ActivateExpenseUseCase $activateExpenseUseCase,
        private readonly DeactivateExpenseUseCase $deactivateExpenseUseCase,
        private readonly DeleteExpenseUseCase $deleteExpenseUseCase
    ) {}

    public function create(CreateExpenseRequest $request): JsonResponse
    {
        try {
            $dto = new CreateExpenseDTO(
                name: $request->input('name'),
                value: $request->input('value'),
                paymentDate: new \DateTime($request->input('payment_date')),
                competenceDate: new \DateTime($request->input('competence_date')),
                categoryId: $request->input('category_id'),
                partnerCompanyId: $request->input('partner_company_id'),
                invoiceId: $request->input('invoice_id'),
                isPaid: $request->input('is_paid', false)
            );

            $expense = $this->createExpenseUseCase->execute($dto);

            return response()->json([
                'id' => $expense->getId(),
                'name' => $expense->getName(),
                'value' => $expense->getValue(),
                'payment_date' => $expense->getPaymentDate()->format('Y-m-d'),
                'competence_date' => $expense->getCompetenceDate()->format('Y-m-d'),
                'category_id' => $expense->getCategoryId(),
                'partner_company_id' => $expense->getPartnerCompanyId(),
                'invoice_id' => $expense->getInvoiceId(),
                'is_active' => $expense->isActive(),
                'is_paid' => $expense->isPaid(),
                'created_at' => $expense->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $expense->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function update(int $id, UpdateExpenseRequest $request): JsonResponse
    {
        try {
            $dto = new UpdateExpenseDTO(
                name: $request->input('name'),
                value: $request->input('value'),
                categoryId: $request->input('category_id'),
                paymentDate: new \DateTime($request->input('payment_date')),
                competenceDate: new \DateTime($request->input('competence_date')),
                partnerCompanyId: $request->input('partner_company_id'),
                invoiceId: $request->input('invoice_id'),
                isPaid: $request->input('is_paid', false)
            );

            $expense = $this->updateExpenseUseCase->execute($id, $dto);

            return response()->json([
                'id' => $expense->getId(),
                'name' => $expense->getName(),
                'value' => $expense->getValue(),
                'category_id' => $expense->getCategoryId(),
                'payment_date' => $expense->getPaymentDate()->format('Y-m-d'),
                'competence_date' => $expense->getCompetenceDate()->format('Y-m-d'),
                'partner_company_id' => $expense->getPartnerCompanyId(),
                'invoice_id' => $expense->getInvoiceId(),
                'is_active' => $expense->isActive(),
                'is_paid' => $expense->isPaid(),
                'created_at' => $expense->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $expense->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function list(): JsonResponse
    {
        try {
            $expenses = $this->listExpensesUseCase->execute();
            
            $response = array_map(function ($expense) {
                return [
                    'id' => $expense->getId(),
                    'name' => $expense->getName(),
                    'value' => $expense->getValue(),
                    'payment_date' => $expense->getPaymentDate()?->format('Y-m-d'),
                    'competence_date' => $expense->getCompetenceDate()?->format('Y-m-d'),
                    'category_id' => $expense->getCategoryId(),
                    'partner_company_id' => $expense->getPartnerCompanyId(),
                    'invoice_id' => $expense->getInvoiceId(),
                    'is_active' => $expense->isActive(),
                    'is_paid' => $expense->isPaid(),
                    'created_at' => $expense->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                    'updated_at' => $expense->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
                ];
            }, $expenses);
            
            return response()->json($response);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function findById(int $id): JsonResponse
    {
        try {
            $dto = new FindExpenseByIdDTO($id);
            $expense = $this->findExpenseByIdUseCase->execute($dto);
            
            if (!$expense) {
                return response()->json(['message' => 'Despesa não encontrada'], 404);
            }
            
            return response()->json([
                'id' => $expense->getId(),
                'name' => $expense->getName(),
                'value' => $expense->getValue(),
                'payment_date' => $expense->getPaymentDate()?->format('Y-m-d'),
                'competence_date' => $expense->getCompetenceDate()?->format('Y-m-d'),
                'category_id' => $expense->getCategoryId(),
                'partner_company_id' => $expense->getPartnerCompanyId(),
                'invoice_id' => $expense->getInvoiceId(),
                'is_active' => $expense->isActive(),
                'is_paid' => $expense->isPaid(),
                'created_at' => $expense->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $expense->getUpdatedAt()?->format('Y-m-d\TH:i:s.u\Z')
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function activate(int $id): JsonResponse
    {
        try {
            $this->activateExpenseUseCase->execute($id);
            return response()->json(['message' => 'Despesa ativada com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function deactivate(int $id): JsonResponse
    {
        try {
            $this->deactivateExpenseUseCase->execute($id);
            return response()->json(['message' => 'Despesa desativada com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->deleteExpenseUseCase->execute($id);
            return response()->json(['message' => 'Despesa excluída com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }
} 