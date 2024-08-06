<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_expense_category()
    {
        $category = ExpenseCategory::create([
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação e refeições',
            'is_active' => true
        ]);

        $this->assertDatabaseHas('expense_categories', [
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação e refeições'
        ]);
    }

    public function test_can_archive_expense_category()
    {
        $category = ExpenseCategory::create([
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação e refeições',
            'is_active' => true
        ]);

        $category->update(['is_active' => false]);

        $this->assertDatabaseHas('expense_categories', [
            'id' => $category->id,
            'is_active' => false
        ]);
    }

    public function test_can_find_expense_category_by_name()
    {
        ExpenseCategory::create([
            'name' => 'Alimentação',
            'description' => 'Despesas com alimentação e refeições',
            'is_active' => true
        ]);

        $category = ExpenseCategory::where('name', 'Alimentação')->first();
        
        $this->assertNotNull($category);
        $this->assertEquals('Despesas com alimentação e refeições', $category->description);
    }
} 