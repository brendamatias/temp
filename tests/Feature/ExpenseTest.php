<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\Expense;
use App\Infrastructure\Database\Models\ExpenseCategory;
use App\Infrastructure\Database\Models\Invoice;
use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    private PartnerCompany $company;
    private ExpenseCategory $category;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = PartnerCompany::create([
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190',
            'is_active' => true
        ]);

        $this->category = ExpenseCategory::create([
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação e refeições',
            'is_active' => true
        ]);

        $this->invoice = Invoice::create([
            'number' => 'NF-001',
            'value' => 1500.00,
            'service_description' => 'Desenvolvimento de software',
            'competence_month' => now(),
            'receipt_date' => now(),
            'partner_company_id' => $this->company->id,
            'is_active' => true
        ]);
    }

    public function test_can_create_expense()
    {
        $expense = Expense::create([
            'name' => 'Almoço com cliente',
            'value' => 150.00,
            'payment_date' => now(),
            'competence_date' => now(),
            'category_id' => $this->category->id,
            'partner_company_id' => $this->company->id,
            'invoice_id' => $this->invoice->id,
            'is_active' => true
        ]);

        $this->assertDatabaseHas('expenses', [
            'name' => 'Almoço com cliente',
            'value' => 150,
            'category_id' => $this->category->id,
            'partner_company_id' => $this->company->id,
            'invoice_id' => $this->invoice->id
        ]);
    }

    public function test_can_find_expense_by_name()
    {
        Expense::create([
            'name' => 'Almoço com cliente',
            'value' => 150.00,
            'payment_date' => now(),
            'competence_date' => now(),
            'category_id' => $this->category->id,
            'partner_company_id' => $this->company->id,
            'invoice_id' => $this->invoice->id,
            'is_active' => true
        ]);

        $expense = Expense::where('name', 'Almoço com cliente')->first();
        
        $this->assertNotNull($expense);
        $this->assertEquals(150.00, $expense->value);
    }

    public function test_can_find_expenses_by_category()
    {
        Expense::create([
            'name' => 'Almoço com cliente',
            'value' => 150.00,
            'payment_date' => now(),
            'competence_date' => now(),
            'category_id' => $this->category->id,
            'partner_company_id' => $this->company->id,
            'invoice_id' => $this->invoice->id,
            'is_active' => true
        ]);

        $expenses = Expense::where('category_id', $this->category->id)->get();
        
        $this->assertCount(1, $expenses);
        $this->assertEquals('Almoço com cliente', $expenses->first()->name);
    }

    public function test_can_soft_delete_expense()
    {
        $expense = Expense::create([
            'name' => 'Almoço com cliente',
            'value' => 150.00,
            'payment_date' => now(),
            'competence_date' => now(),
            'category_id' => $this->category->id,
            'partner_company_id' => $this->company->id,
            'invoice_id' => $this->invoice->id,
            'is_active' => true
        ]);

        $expense->delete();

        $this->assertSoftDeleted('expenses', [
            'id' => $expense->id
        ]);
    }
} 