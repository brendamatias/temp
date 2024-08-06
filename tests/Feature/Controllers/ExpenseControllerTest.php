<?php

namespace Tests\Feature\Controllers;

use App\Infrastructure\Database\Models\Expense;
use App\Infrastructure\Database\Models\ExpenseCategory;
use App\Infrastructure\Database\Models\PartnerCompany;
use App\Infrastructure\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private ExpenseCategory $category;
    private PartnerCompany $partnerCompany;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->category = ExpenseCategory::factory()->create();
        $this->partnerCompany = PartnerCompany::factory()->create();
    }

    public function test_should_create_expense(): void
    {
        $expenseData = [
            'name' => 'Aluguel',
            'value' => 1500.00,
            'payment_date' => '2024-03-20',
            'competence_date' => '2024-03-01',
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', $expenseData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'value',
                'payment_date',
                'competence_date',
                'category_id',
                'partner_company_id',
                'invoice_id',
                'is_active',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('expenses', [
            'name' => $expenseData['name'],
            'value' => $expenseData['value'],
            'category_id' => $expenseData['category_id'],
            'partner_company_id' => $expenseData['partner_company_id']
        ]);
    }

    public function test_should_update_expense(): void
    {
        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $updateData = [
            'name' => 'Aluguel Atualizado',
            'value' => 1600.00,
            'payment_date' => '2024-04-20',
            'competence_date' => '2024-04-01',
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/expenses/{$expense->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'value',
                'payment_date',
                'competence_date',
                'category_id',
                'partner_company_id',
                'invoice_id',
                'is_active',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'name' => $updateData['name'],
            'value' => $updateData['value']
        ]);
    }

    public function test_should_list_expenses(): void
    {
        Expense::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/expenses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'value',
                    'payment_date',
                    'competence_date',
                    'category_id',
                    'partner_company_id',
                    'invoice_id',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_should_find_expense_by_id(): void
    {
        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'value',
                'payment_date',
                'competence_date',
                'category_id',
                'partner_company_id',
                'invoice_id',
                'is_active',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_should_activate_expense(): void
    {
        $expense = Expense::factory()->create([
            'is_active' => false,
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/expenses/{$expense->id}/activate");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Despesa ativada com sucesso']);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'is_active' => true
        ]);
    }

    public function test_should_deactivate_expense(): void
    {
        $expense = Expense::factory()->create([
            'is_active' => true,
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/expenses/{$expense->id}/deactivate");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Despesa desativada com sucesso']);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'is_active' => false
        ]);
    }

    public function test_should_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'category_id' => $this->category->id,
            'partner_company_id' => $this->partnerCompany->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/expenses/{$expense->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Despesa excluída com sucesso']);

        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_should_not_allow_unauthorized_access(): void
    {
        $response = $this->getJson('/api/expenses');
        $response->assertStatus(401);
    }

    public function test_should_validate_required_fields_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'value',
                'payment_date',
                'competence_date',
                'category_id'
            ]);
    }

    public function test_should_validate_field_types_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'name' => 123,
                'value' => 'invalid',
                'payment_date' => 'invalid-date',
                'competence_date' => 'invalid-date',
                'category_id' => 'invalid',
                'partner_company_id' => 'invalid'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'value',
                'payment_date',
                'competence_date',
                'category_id',
                'partner_company_id'
            ]);
    }

    public function test_should_validate_category_exists(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'name' => 'Teste',
                'value' => 100.00,
                'payment_date' => '2024-03-20',
                'competence_date' => '2024-03-01',
                'category_id' => 999999,
                'partner_company_id' => $this->partnerCompany->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_should_validate_partner_company_exists(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expenses', [
                'name' => 'Teste',
                'value' => 100.00,
                'payment_date' => '2024-03-20',
                'competence_date' => '2024-03-01',
                'category_id' => $this->category->id,
                'partner_company_id' => 999999
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['partner_company_id']);
    }
} 