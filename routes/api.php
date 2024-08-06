<?php

use App\Infrastructure\Http\Controllers\AuthController;
use App\Infrastructure\Http\Controllers\InvoiceController;
use App\Infrastructure\Http\Controllers\PartnerCompanyController;
use App\Infrastructure\Http\Controllers\PreferenceController;
use App\Infrastructure\Http\Controllers\ReportController;
use App\Infrastructure\Http\Controllers\ExpenseCategoryController;
use App\Infrastructure\Http\Controllers\ExpenseController;
use App\Infrastructure\Http\Controllers\DashboardController;
use App\Infrastructure\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at
            ]
        ]);
    });
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [ReportController::class, 'index']);

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::post('/', [InvoiceController::class, 'create'])->name('create');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::get('/', [InvoiceController::class, 'list'])->name('list');
        Route::get('/{id}', [InvoiceController::class, 'findById'])->name('find');
    });

    Route::prefix('partner-companies')->name('partner-companies.')->group(function () {
        Route::post('/', [PartnerCompanyController::class, 'create'])->name('create');
        Route::put('/{id}', [PartnerCompanyController::class, 'update'])->name('update');
        Route::get('/', [PartnerCompanyController::class, 'list'])->name('list');
        Route::get('/{id}', [PartnerCompanyController::class, 'findById'])->name('find');
        Route::get('/document/{document}', [PartnerCompanyController::class, 'findByDocument'])->name('find-by-document');
        Route::get('/search', [PartnerCompanyController::class, 'search'])->name('search');
    });

    Route::prefix('preferences')->name('preferences.')->group(function () {
        Route::get('/', [PreferenceController::class, 'show'])->name('show');
        Route::put('/', [PreferenceController::class, 'update'])->name('update');
        Route::post('/change-password', [PreferenceController::class, 'changePassword'])->name('change-password');
    });

    Route::prefix('expense-categories')->name('expense-categories.')->group(function () {
        Route::post('/', [ExpenseCategoryController::class, 'create'])->name('create');
        Route::put('/{id}', [ExpenseCategoryController::class, 'update'])->name('update');
        Route::get('/', [ExpenseCategoryController::class, 'list'])->name('list');
        Route::get('/{id}', [ExpenseCategoryController::class, 'findById'])->name('find');
        Route::post('/{id}/activate', [ExpenseCategoryController::class, 'activate'])->name('activate');
        Route::post('/{id}/deactivate', [ExpenseCategoryController::class, 'deactivate'])->name('deactivate');
    });

    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::post('/', [ExpenseController::class, 'create'])->name('create');
        Route::put('/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::get('/', [ExpenseController::class, 'list'])->name('list');
        Route::get('/{id}', [ExpenseController::class, 'findById'])->name('find');
        Route::post('/{id}/activate', [ExpenseController::class, 'activate'])->name('activate');
        Route::post('/{id}/deactivate', [ExpenseController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{id}', [ExpenseController::class, 'delete'])->name('delete');
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    Route::get('/user', [ProfileController::class, 'show']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
});

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    return response()->json([
        'message' => 'Credenciais inválidas'
    ], 401);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso']);
    });

    Route::get('/dashboard', [ReportController::class, 'index']);

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::post('/', [InvoiceController::class, 'create'])->name('create');
        Route::put('/{id}', [InvoiceController::class, 'update'])->name('update');
        Route::get('/', [InvoiceController::class, 'list'])->name('list');
        Route::get('/{id}', [InvoiceController::class, 'findById'])->name('find');
    });

    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/preferences', [PreferenceController::class, 'show']);
    Route::put('/preferences', [PreferenceController::class, 'update']);
    Route::post('/change-password', [PreferenceController::class, 'changePassword']);

    Route::get('/user', [ProfileController::class, 'show']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
}); 