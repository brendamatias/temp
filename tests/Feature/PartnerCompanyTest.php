<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerCompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_partner_company()
    {
        $company = PartnerCompany::create([
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190',
            'is_active' => true
        ]);

        $this->assertDatabaseHas('partner_companies', [
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190'
        ]);
    }

    public function test_can_find_partner_company_by_document()
    {
        PartnerCompany::create([
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190',
            'is_active' => true
        ]);

        $company = PartnerCompany::where('document', '12345678000190')->first();
        
        $this->assertNotNull($company);
        $this->assertEquals('Tech Solutions', $company->name);
    }

    public function test_can_soft_delete_partner_company()
    {
        $company = PartnerCompany::create([
            'name' => 'Tech Solutions',
            'legal_name' => 'Tech Solutions Ltda',
            'document' => '12345678000190',
            'is_active' => true
        ]);

        $company->delete();

        $this->assertSoftDeleted('partner_companies', [
            'id' => $company->id
        ]);
    }
} 