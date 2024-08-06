<?php

namespace Tests\Feature\Controllers;

use App\Infrastructure\Database\Models\PartnerCompany;
use App\Infrastructure\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerCompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_create_partner_company(): void
    {
        $data = [
            'name' => 'Empresa Teste',
            'legal_name' => 'Empresa Teste LTDA',
            'document' => '12345678901234',
            'is_active' => true
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/partner-companies', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Empresa parceira criada com sucesso'
            ]);

        $this->assertDatabaseHas('partner_companies', [
            'name' => $data['name'],
            'legal_name' => $data['legal_name'],
            'document' => $data['document'],
            'is_active' => $data['is_active']
        ]);
    }

    public function test_can_update_partner_company(): void
    {
        $company = PartnerCompany::factory()->create();

        $data = [
            'name' => 'Empresa Atualizada',
            'legal_name' => 'Empresa Atualizada LTDA',
            'document' => '98765432109876',
            'is_active' => false
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/partner-companies/{$company->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Empresa parceira atualizada com sucesso'
            ]);

        $this->assertDatabaseHas('partner_companies', [
            'id' => $company->id,
            'name' => $data['name'],
            'legal_name' => $data['legal_name'],
            'document' => $data['document'],
            'is_active' => $data['is_active']
        ]);
    }

    public function test_can_list_partner_companies(): void
    {
        PartnerCompany::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/partner-companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'legal_name',
                        'document',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_can_find_partner_company_by_id(): void
    {
        $company = PartnerCompany::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/partner-companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'legal_name',
                    'document',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_returns_404_when_partner_company_not_found(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/partner-companies/999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Empresa parceira não encontrada'
            ]);
    }

    public function test_validates_required_fields_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/partner-companies', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'legal_name', 'document']);
    }

    public function test_validates_required_fields_on_update(): void
    {
        $company = PartnerCompany::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/partner-companies/{$company->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'legal_name', 'document']);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/partner-companies');

        $response->assertStatus(401);
    }
} 