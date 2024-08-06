<?php

namespace Tests\Feature\Controllers;

use App\Infrastructure\Database\Models\ExpenseCategory;
use App\Infrastructure\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryControllerTest extends TestCase
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

    public function test_should_create_expense_category(): void
    {
        $categoryData = [
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expense-categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('expense_categories', $categoryData);
    }

    public function test_should_update_expense_category(): void
    {
        $category = ExpenseCategory::factory()->create();
        $updateData = [
            'name' => 'Alimentação Atualizada',
            'description' => 'Descrição atualizada'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/expense-categories/{$category->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('expense_categories', $updateData);
    }

    public function test_should_list_expense_categories(): void
    {
        ExpenseCategory::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/expense-categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'description',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_should_find_expense_category_by_id(): void
    {
        $category = ExpenseCategory::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/expense-categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_should_activate_expense_category(): void
    {
        $category = ExpenseCategory::factory()->create(['is_active' => false]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/expense-categories/{$category->id}/activate");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Categoria ativada com sucesso']);

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'is_active' => true
        ]);
    }

    public function test_should_deactivate_expense_category(): void
    {
        $category = ExpenseCategory::factory()->create(['is_active' => true]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/expense-categories/{$category->id}/deactivate");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Categoria desativada com sucesso']);

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'is_active' => false
        ]);
    }

    public function test_should_not_allow_unauthorized_access(): void
    {
        $response = $this->getJson('/api/expense-categories');
        $response->assertStatus(401);
    }

    public function test_should_validate_required_fields_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expense-categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description']);
    }

    public function test_should_validate_field_types_on_create(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/expense-categories', [
                'name' => 123,
                'description' => 456
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'description']);
    }
} 