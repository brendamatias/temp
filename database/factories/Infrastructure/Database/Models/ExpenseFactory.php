<?php

namespace Database\Factories\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Expense;
use App\Infrastructure\Database\Models\ExpenseCategory;
use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'value' => $this->faker->randomFloat(2, 10, 1000),
            'payment_date' => $this->faker->date(),
            'competence_date' => $this->faker->date(),
            'category_id' => ExpenseCategory::factory(),
            'partner_company_id' => PartnerCompany::factory(),
            'is_active' => true
        ];
    }
} 