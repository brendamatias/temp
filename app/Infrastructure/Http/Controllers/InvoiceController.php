<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\DTOs\Invoice\CreateInvoiceDTO;
use App\Application\DTOs\Invoice\UpdateInvoiceDTO;
use App\Application\DTOs\Invoice\ListInvoicesDTO;
use App\Application\DTOs\Invoice\FindInvoiceByIdDTO;
use App\Application\DTOs\Invoice\InvoiceResponseDTO;
use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\UseCases\Invoice\UpdateInvoiceUseCase;
use App\Application\UseCases\Invoice\ListInvoicesUseCase;
use App\Application\UseCases\Invoice\FindInvoiceByIdUseCase;
use App\Application\Validators\Invoice\InvoiceRequestValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly CreateInvoiceUseCase $createInvoiceUseCase,
        private readonly UpdateInvoiceUseCase $updateInvoiceUseCase,
        private readonly ListInvoicesUseCase $listInvoicesUseCase,
        private readonly FindInvoiceByIdUseCase $findInvoiceByIdUseCase,
        private readonly InvoiceRequestValidator $requestValidator
    ) {}

    public function create(Request $request): JsonResponse
    {
        try {
            $errors = $this->requestValidator->validateCreate($request);
            if (!empty($errors)) {
                return response()->json(['errors' => $errors], 422);
            }

            $dto = new CreateInvoiceDTO(
                number: $request->input('number'),
                partnerCompanyId: $request->input('partnerCompanyId'),
                value: $request->input('value'),
                serviceDescription: $request->input('service_description'),
                competenceMonth: new \DateTime($request->input('competence_month')),
                receiptDate: new \DateTime($request->input('receipt_date'))
            );

            $this->createInvoiceUseCase->execute($dto);

            return response()->json(['message' => 'Nota fiscal criada com sucesso'], 201);
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

            $dto = new UpdateInvoiceDTO(
                id: $id,
                number: $request->input('number'),
                value: $request->input('value'),
                serviceDescription: $request->input('service_description'),
                competenceMonth: $request->has('competence_month') ? new \DateTime($request->input('competence_month')) : null,
                receiptDate: $request->has('receipt_date') ? new \DateTime($request->input('receipt_date')) : null
            );

            $this->updateInvoiceUseCase->execute($dto);

            return response()->json(['message' => 'Nota fiscal atualizada com sucesso']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $dto = new ListInvoicesDTO(
                includeInactive: $request->boolean('includeInactive', false)
            );

            $invoices = $this->listInvoicesUseCase->execute($dto);

            return response()->json($invoices);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function findById(int $id): JsonResponse
    {
        try {
            $dto = new FindInvoiceByIdDTO(id: $id);
            $invoice = $this->findInvoiceByIdUseCase->execute($dto);

            if (!$invoice) {
                return response()->json(['message' => 'Nota fiscal não encontrada'], 404);
            }

            $responseDto = InvoiceResponseDTO::fromEntity($invoice);
            return response()->json($responseDto->toArray());
        } catch (\InvalidArgumentException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        }
    }

    public function store(Request $request): JsonResponse
    {
        return $this->create($request);
    }
} 