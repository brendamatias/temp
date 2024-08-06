<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\UseCases\Dashboard\GetDashboardDataUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private GetDashboardDataUseCase $getDashboardDataUseCase
    ) {}

    public function index(Request $request): JsonResponse
    {
        $year = $request->query('year', date('Y'));
        
        $data = $this->getDashboardDataUseCase->execute((int) $year);
        
        return response()->json($data);
    }
} 