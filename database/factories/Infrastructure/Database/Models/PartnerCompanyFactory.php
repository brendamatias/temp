<?php

namespace Database\Factories\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\PartnerCompany;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerCompanyFactory extends Factory
{
    protected $model = PartnerCompany::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'name' => $name,
            'legal_name' => $name . ' LTDA',
            'document' => $this->faker->numerify('##############'),
            'is_active' => true
        ];
    }
} 