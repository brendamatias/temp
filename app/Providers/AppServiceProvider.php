<?php

namespace App\Providers;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Services\AuthService;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\UseCases\Invoice\UpdateInvoiceUseCase;
use App\Application\UseCases\Invoice\ListInvoicesUseCase;
use App\Application\UseCases\Invoice\FindInvoiceByIdUseCase;
use App\Application\Validators\Invoice\InvoiceRequestValidator;
use App\Infrastructure\Http\Controllers\InvoiceController;
use App\Application\UseCases\PartnerCompany\CreatePartnerCompanyUseCase;
use App\Application\UseCases\PartnerCompany\UpdatePartnerCompanyUseCase;
use App\Application\UseCases\PartnerCompany\ListPartnerCompaniesUseCase;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByIdUseCase;
use App\Application\Validators\PartnerCompany\PartnerCompanyRequestValidator;
use App\Infrastructure\Http\Controllers\PartnerCompanyController;
use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\PartnerCompanyRepository;
use App\Infrastructure\Repositories\PartnerCompanyRepository as PartnerCompanyRepositoryImpl;
use App\Domain\Repositories\ExpenseCategoryRepositoryInterface;
use App\Infrastructure\Repositories\ExpenseCategoryRepository;
use App\Domain\Repositories\ExpenseRepositoryInterface;
use App\Infrastructure\Repositories\ExpenseRepository;
use App\Application\UseCases\Expense\CreateExpenseUseCase;
use App\Application\UseCases\Expense\UpdateExpenseUseCase;
use App\Application\UseCases\Expense\ListExpensesUseCase;
use App\Application\UseCases\Expense\FindExpenseByIdUseCase;
use App\Application\UseCases\Expense\ActivateExpenseUseCase;
use App\Application\UseCases\Expense\DeactivateExpenseUseCase;
use App\Application\UseCases\Expense\DeleteExpenseUseCase;
use App\Infrastructure\Http\Controllers\ExpenseController;
use App\Domain\Repositories\InvoiceRepository;
use App\Infrastructure\Repositories\InvoiceRepository as InvoiceRepositoryImpl;
use App\Application\Validators\Invoice\CreateInvoiceValidator;
use App\Application\Validators\Invoice\UpdateInvoiceValidator;
use App\Application\Validators\Invoice\FindInvoiceByIdValidator;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByDocumentUseCase;
use App\Application\UseCases\PartnerCompany\FindPartnerCompanyByNameUseCase;
use App\Application\UseCases\Dashboard\GetDashboardDataUseCase;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(AuthService::class);
        
        $this->app->bind(InvoiceRepository::class, function ($app) {
            return new InvoiceRepositoryImpl(
                $app->make(PartnerCompanyRepository::class)
            );
        });
        
        $this->app->singleton(CreateInvoiceUseCase::class, function ($app) {
            return new CreateInvoiceUseCase(
                $app->make(InvoiceRepository::class),
                $app->make(PartnerCompanyRepository::class),
                $app->make(CreateInvoiceValidator::class)
            );
        });
        
        $this->app->singleton(UpdateInvoiceUseCase::class, function ($app) {
            return new UpdateInvoiceUseCase(
                $app->make(InvoiceRepository::class),
                $app->make(PartnerCompanyRepository::class),
                $app->make(UpdateInvoiceValidator::class)
            );
        });
        
        $this->app->singleton(ListInvoicesUseCase::class, function ($app) {
            return new ListInvoicesUseCase(
                $app->make(InvoiceRepository::class)
            );
        });
        
        $this->app->singleton(FindInvoiceByIdUseCase::class, function ($app) {
            return new FindInvoiceByIdUseCase(
                $app->make(InvoiceRepository::class),
                $app->make(FindInvoiceByIdValidator::class)
            );
        });
        
        $this->app->singleton(CreateInvoiceValidator::class, function ($app) {
            return new CreateInvoiceValidator(
                $app->make(InvoiceRepository::class),
                $app->make(PartnerCompanyRepository::class)
            );
        });
        
        $this->app->singleton(UpdateInvoiceValidator::class, function ($app) {
            return new UpdateInvoiceValidator(
                $app->make(InvoiceRepository::class),
                $app->make(PartnerCompanyRepository::class)
            );
        });
        
        $this->app->singleton(FindInvoiceByIdValidator::class);
        $this->app->singleton(InvoiceRequestValidator::class);
        
        $this->app->singleton(PartnerCompanyRepository::class, function ($app) {
            return new PartnerCompanyRepositoryImpl();
        });

        $this->app->singleton(CreatePartnerCompanyUseCase::class, function ($app) {
            return new CreatePartnerCompanyUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(UpdatePartnerCompanyUseCase::class, function ($app) {
            return new UpdatePartnerCompanyUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(ListPartnerCompaniesUseCase::class, function ($app) {
            return new ListPartnerCompaniesUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(FindPartnerCompanyByIdUseCase::class, function ($app) {
            return new FindPartnerCompanyByIdUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(PartnerCompanyRequestValidator::class);
        
        $this->app->bind(InvoiceController::class, function ($app) {
            return new InvoiceController(
                $app->make(CreateInvoiceUseCase::class),
                $app->make(UpdateInvoiceUseCase::class),
                $app->make(ListInvoicesUseCase::class),
                $app->make(FindInvoiceByIdUseCase::class),
                $app->make(InvoiceRequestValidator::class)
            );
        });

        $this->app->bind(PartnerCompanyController::class, function ($app) {
            return new PartnerCompanyController(
                $app->make(CreatePartnerCompanyUseCase::class),
                $app->make(UpdatePartnerCompanyUseCase::class),
                $app->make(ListPartnerCompaniesUseCase::class),
                $app->make(FindPartnerCompanyByIdUseCase::class),
                $app->make(FindPartnerCompanyByDocumentUseCase::class),
                $app->make(FindPartnerCompanyByNameUseCase::class),
                $app->make(PartnerCompanyRequestValidator::class)
            );
        });

        $this->app->bind(\App\Domain\Repositories\PreferencesRepository::class, \App\Infrastructure\Repositories\PreferencesRepository::class);
        $this->app->singleton(\App\Application\Validators\Preferences\CreatePreferencesValidator::class);
        $this->app->singleton(\App\Application\Validators\Preferences\UpdatePreferencesValidator::class);
        $this->app->singleton(\App\Application\Validators\Preferences\GetPreferencesValidator::class);
        $this->app->singleton(\App\Application\Validators\Preferences\GetPreferenceByKeyValidator::class);
        
        $this->app->singleton(\App\Application\UseCases\Preferences\CreatePreferencesUseCase::class);
        $this->app->singleton(\App\Application\UseCases\Preferences\UpdatePreferencesUseCase::class);
        $this->app->singleton(\App\Application\UseCases\Preferences\GetPreferencesUseCase::class);
        $this->app->singleton(\App\Application\UseCases\Preferences\GetPreferenceByKeyUseCase::class);

        $this->app->bind(ExpenseCategoryRepositoryInterface::class, ExpenseCategoryRepository::class);

        $this->app->bind(
            \App\Domain\Repositories\ExpenseRepositoryInterface::class,
            \App\Infrastructure\Repositories\ExpenseRepository::class
        );

        $this->app->singleton(CreateExpenseUseCase::class, function ($app) {
            return new CreateExpenseUseCase(
                $app->make(ExpenseRepositoryInterface::class),
                $app->make(ExpenseCategoryRepositoryInterface::class),
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(UpdateExpenseUseCase::class, function ($app) {
            return new UpdateExpenseUseCase(
                $app->make(ExpenseRepositoryInterface::class),
                $app->make(ExpenseCategoryRepositoryInterface::class),
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->singleton(ListExpensesUseCase::class, function ($app) {
            return new ListExpensesUseCase(
                $app->make(ExpenseRepositoryInterface::class)
            );
        });

        $this->app->singleton(FindExpenseByIdUseCase::class, function ($app) {
            return new FindExpenseByIdUseCase(
                $app->make(ExpenseRepositoryInterface::class)
            );
        });

        $this->app->singleton(ActivateExpenseUseCase::class, function ($app) {
            return new ActivateExpenseUseCase(
                $app->make(ExpenseRepositoryInterface::class)
            );
        });

        $this->app->singleton(DeactivateExpenseUseCase::class, function ($app) {
            return new DeactivateExpenseUseCase(
                $app->make(ExpenseRepositoryInterface::class)
            );
        });

        $this->app->singleton(DeleteExpenseUseCase::class, function ($app) {
            return new DeleteExpenseUseCase(
                $app->make(ExpenseRepositoryInterface::class)
            );
        });

        $this->app->bind(ExpenseController::class, function ($app) {
            return new ExpenseController(
                $app->make(CreateExpenseUseCase::class),
                $app->make(UpdateExpenseUseCase::class),
                $app->make(ListExpensesUseCase::class),
                $app->make(FindExpenseByIdUseCase::class),
                $app->make(ActivateExpenseUseCase::class),
                $app->make(DeactivateExpenseUseCase::class),
                $app->make(DeleteExpenseUseCase::class)
            );
        });

        $this->app->bind(FindPartnerCompanyByDocumentUseCase::class, function ($app) {
            return new FindPartnerCompanyByDocumentUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->bind(FindPartnerCompanyByNameUseCase::class, function ($app) {
            return new FindPartnerCompanyByNameUseCase(
                $app->make(PartnerCompanyRepository::class)
            );
        });

        $this->app->bind(GetDashboardDataUseCase::class, function ($app) {
            return new GetDashboardDataUseCase(
                $app->make(\App\Domain\Repositories\InvoiceRepository::class),
                $app->make(\App\Domain\Repositories\ExpenseRepositoryInterface::class)
            );
        });

        $this->app->bind(
            \App\Domain\Repositories\InvoiceRepository::class,
            \App\Infrastructure\Repositories\InvoiceRepository::class
        );
    }

    public function boot(): void
    {}
}
