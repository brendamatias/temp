<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\Invoice;
use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private PartnerCompany $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = PartnerCompany::create([
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190',
            'is_active' => true
        ]);
    }

    public function test_can_create_invoice()
    {
        $invoice = Invoice::create([
            'number' => 'NF-001',
            'value' => 1500.00,
            'service_description' => 'Desenvolvimento de software',
            'competence_month' => now(),
            'receipt_date' => now(),
            'partner_company_id' => $this->company->id,
            'is_active' => true
        ]);

        $this->assertDatabaseHas('invoices', [
            'number' => 'NF-001',
            'value' => 1500.00,
            'service_description' => 'Desenvolvimento de software',
            'partner_company_id' => $this->company->id
        ]);
    }

    public function test_can_find_invoice_by_number()
    {
        Invoice::create([
            'number' => 'NF-001',
            'value' => 1500.00,
            'service_description' => 'Desenvolvimento de software',
            'competence_month' => now(),
            'receipt_date' => now(),
            'partner_company_id' => $this->company->id,
            'is_active' => true
        ]);

        $invoice = Invoice::where('number', 'NF-001')->first();
        
        $this->assertNotNull($invoice);
        $this->assertEquals(1500.00, $invoice->value);
    }

    public function test_can_soft_delete_invoice()
    {
        $invoice = Invoice::create([
            'number' => 'NF-001',
            'value' => 1500.00,
            'service_description' => 'Desenvolvimento de software',
            'competence_month' => now(),
            'receipt_date' => now(),
            'partner_company_id' => $this->company->id,
            'is_active' => true
        ]);

        $invoice->delete();

        $this->assertSoftDeleted('invoices', [
            'id' => $invoice->id
        ]);
    }
} 