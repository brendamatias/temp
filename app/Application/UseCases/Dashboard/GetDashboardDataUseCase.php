<?php

namespace App\Application\UseCases\Dashboard;

use App\Domain\Repositories\InvoiceRepository;
use App\Domain\Repositories\ExpenseRepositoryInterface;

class GetDashboardDataUseCase
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly ExpenseRepositoryInterface $expenseRepository
    ) {}

    public function execute(string $year): array
    {
        $totalRevenue = $this->invoiceRepository->getTotalRevenueByYear($year);
        $monthlyRevenue = $this->invoiceRepository->getMonthlyRevenueByYear($year);
        $monthlyExpenses = $this->expenseRepository->getMonthlyExpensesByYear($year);
        $categoryExpenses = $this->expenseRepository->getExpensesByCategory($year);

        $monthlyBalance = [];
        $months = array_fill_keys(range(1, 12), 0);
        
        foreach ($monthlyRevenue as $revenue) {
            $month = (int) $revenue['month'];
            $months[$month] = $revenue['value'];
        }
    
        foreach ($monthlyExpenses as $expense) {
            $month = (int) $expense['month'];
            $months[$month] -= $expense['value'];
        }
        
        foreach ($months as $month => $balance) {
            $monthlyBalance[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'value' => $balance
            ];
        }

        return [
            'meiLimit' => [
                'total' => 81000,
                'used' => $totalRevenue,
                'available' => 81000 - $totalRevenue
            ],
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyBalance' => $monthlyBalance,
            'categoryExpenses' => $categoryExpenses
        ];
    }
} 