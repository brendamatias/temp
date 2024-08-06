<?php

namespace Database\Factories;

use App\Domain\Entities\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('NF####'),
            'partner_company_id' => PartnerCompany::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'issue_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
            'is_active' => true
        ];
    }
} 