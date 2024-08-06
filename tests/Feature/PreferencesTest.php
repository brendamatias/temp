<?php

namespace Tests\Feature;

use App\Infrastructure\Database\Models\Preference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_preferences()
    {
        $user = \App\Infrastructure\Database\Models\User::factory()->create();
        $preferences = Preference::create([
            'user_id' => $user->id,
            'theme' => 'dark',
            'language' => 'pt_BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'mei_annual_limit' => 81000.00,
            'mei_alert_threshold' => 75000.00,
            'mei_monthly_alert_day' => 15
        ]);

        $this->assertDatabaseHas('preferences', [
            'user_id' => $user->id,
            'theme' => 'dark',
            'language' => 'pt_BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'mei_annual_limit' => 81000.00,
            'mei_alert_threshold' => 75000.00,
            'mei_monthly_alert_day' => 15
        ]);
    }

    public function test_can_update_preferences()
    {
        $user = \App\Infrastructure\Database\Models\User::factory()->create();
        $preferences = Preference::create([
            'user_id' => $user->id,
            'theme' => 'dark',
            'language' => 'pt_BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'mei_annual_limit' => 81000.00,
            'mei_alert_threshold' => 75000.00,
            'mei_monthly_alert_day' => 15
        ]);

        $preferences->update([
            'theme' => 'light',
            'notifications_enabled' => false
        ]);

        $this->assertDatabaseHas('preferences', [
            'id' => $preferences->id,
            'user_id' => $user->id,
            'theme' => 'light',
            'notifications_enabled' => false
        ]);
    }

    public function test_can_find_preferences_by_theme()
    {
        $user = \App\Infrastructure\Database\Models\User::factory()->create();
        Preference::create([
            'user_id' => $user->id,
            'theme' => 'dark',
            'language' => 'pt_BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'mei_annual_limit' => 81000.00,
            'mei_alert_threshold' => 75000.00,
            'mei_monthly_alert_day' => 15
        ]);

        $preferences = Preference::where('theme', 'dark')->first();
        
        $this->assertNotNull($preferences);
        $this->assertEquals('pt_BR', $preferences->language);
    }

    public function test_can_validate_mei_limits()
    {
        $user = \App\Infrastructure\Database\Models\User::factory()->create();
        $preferences = Preference::create([
            'user_id' => $user->id,
            'theme' => 'dark',
            'language' => 'pt_BR',
            'currency' => 'BRL',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'notifications_enabled' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'mei_annual_limit' => 81000.00,
            'mei_alert_threshold' => 75000.00,
            'mei_monthly_alert_day' => 15
        ]);

        $this->assertLessThan(
            $preferences->mei_annual_limit,
            $preferences->mei_alert_threshold,
            'O limite de alerta deve ser menor que o limite anual'
        );
    }
} 