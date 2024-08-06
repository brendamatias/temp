<?php

namespace Database\Factories\Infrastructure\Database\Models;

use App\Infrastructure\Database\Models\Preference;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Infrastructure\Database\Models\User;

class PreferenceFactory extends Factory
{
    protected $model = Preference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'theme' => $this->faker->randomElement(['LIGHT', 'DARK']),
            'language' => $this->faker->randomElement(['pt-BR', 'en-US']),
            'currency' => $this->faker->randomElement(['BRL', 'USD']),
            'date_format' => $this->faker->randomElement(['d/m/Y', 'Y-m-d']),
            'time_format' => $this->faker->randomElement(['H:i', 'h:i A']),
            'notifications_enabled' => $this->faker->boolean(),
            'email_notifications' => $this->faker->boolean(),
            'sms_notifications' => $this->faker->boolean(),
            'mei_annual_limit' => $this->faker->randomFloat(2, 60000, 81000),
            'mei_alert_threshold' => $this->faker->numberBetween(50, 90),
            'mei_monthly_alert_day' => $this->faker->numberBetween(1, 28)
        ];
    }
} 