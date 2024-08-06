<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\Dashboard\GetDashboardDataUseCase;
use App\Application\UseCases\Invoice\ListInvoicesUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct(
        private readonly ListInvoicesUseCase $listInvoicesUseCase,
        private readonly GetDashboardDataUseCase $getDashboardDataUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $year = $request->query('year', date('Y'));
            $data = $this->getDashboardDataUseCase->execute($year);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao carregar dados do dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateInvoiceReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            $invoices = $this->listInvoicesUseCase->execute($startDate, $endDate);

            $pdf = Pdf::loadView('reports.invoices', [
                'invoices' => $invoices,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            return response()->json([
                'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdf->output())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ], 500);
        }
    }
} 