<?php

namespace Tests\Feature\Controllers;

use App\Infrastructure\Database\Models\Preference;
use App\Infrastructure\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferenceControllerTest extends TestCase
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

    public function test_should_create_preference(): void
    {
        $preferenceData = [
            'theme' => 'DARK',
            'language' => 'en-US',
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'time_format' => 'h:i A',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => true,
            'mei_annual_limit' => 75000.00,
            'mei_alert_threshold' => 85,
            'mei_monthly_alert_day' => 15
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/preferences', $preferenceData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'theme',
                'language',
                'currency',
                'date_format',
                'time_format',
                'notifications_enabled',
                'email_notifications',
                'sms_notifications',
                'mei_annual_limit',
                'mei_alert_threshold',
                'mei_monthly_alert_day',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('preferences', $preferenceData);
    }

    public function test_should_update_preference(): void
    {
        $preference = Preference::factory()->create([
            'user_id' => $this->user->id
        ]);

        $updateData = [
            'theme' => 'DARK',
            'language' => 'en-US'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/preferences/{$preference->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $preference->id,
                'theme' => 'DARK',
                'language' => 'en-US'
            ]);

        $this->assertDatabaseHas('preferences', [
            'id' => $preference->id,
            'theme' => 'DARK',
            'language' => 'en-US'
        ]);
    }

    public function test_should_list_preferences(): void
    {
        Preference::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/preferences');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'theme',
                    'language',
                    'currency',
                    'date_format',
                    'time_format',
                    'notifications_enabled',
                    'email_notifications',
                    'sms_notifications',
                    'mei_annual_limit',
                    'mei_alert_threshold',
                    'mei_monthly_alert_day',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    public function test_should_find_preference_by_id(): void
    {
        $preference = Preference::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/preferences/{$preference->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $preference->id,
                'theme' => $preference->theme,
                'language' => $preference->language,
                'currency' => $preference->currency,
                'date_format' => $preference->date_format,
                'time_format' => $preference->time_format,
                'notifications_enabled' => $preference->notifications_enabled,
                'email_notifications' => $preference->email_notifications,
                'sms_notifications' => $preference->sms_notifications,
                'mei_annual_limit' => $preference->mei_annual_limit,
                'mei_alert_threshold' => $preference->mei_alert_threshold,
                'mei_monthly_alert_day' => $preference->mei_monthly_alert_day
            ]);
    }

    public function test_should_not_allow_unauthorized_access(): void
    {
        $response = $this->getJson('/api/preferences');
        $response->assertStatus(401);
    }

    public function test_should_not_allow_access_to_other_user_preferences(): void
    {
        $otherUser = User::factory()->create();
        $preference = Preference::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/preferences/{$preference->id}");

        $response->assertStatus(403);
    }
} 