<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase4MobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_is_available(): void
    {
        $this->get('/manifest.webmanifest')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertSee('بيت المصور', false)
            ->assertSee('standalone', false);
    }

    public function test_service_worker_is_available(): void
    {
        $this->get('/sw.js')
            ->assertOk()
            ->assertSee('push', false);
    }

    public function test_home_page_links_pwa_assets(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('js/pwa.js', false)
            ->assertSee('theme-color', false);
    }

    public function test_authenticated_user_can_store_push_subscription(): void
    {
        config([
            'push.enabled' => true,
            'push.vapid.public_key' => 'test-public-key',
            'push.vapid.private_key' => 'test-private-key',
        ]);

        $user = User::create([
            'name' => 'مستخدم الإشعارات',
            'email' => 'push-user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $endpoint = 'https://push.example.com/subscription/abc123';

        $this->actingAs($user)
            ->postJson(route('push-subscriptions.store'), [
                'endpoint' => $endpoint,
                'keys' => [
                    'auth' => 'auth-token',
                    'p256dh' => 'public-key',
                ],
            ])
            ->assertOk()
            ->assertJson(['stored' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => $endpoint,
        ]);
    }

    public function test_push_store_route_is_hidden_when_push_disabled(): void
    {
        config(['push.enabled' => false]);

        $user = User::create([
            'name' => 'مستخدم بدون push',
            'email' => 'no-push@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->postJson(route('push-subscriptions.store'), [
                'endpoint' => 'https://push.example.com/subscription/hidden',
                'keys' => [
                    'auth' => 'auth-token',
                    'p256dh' => 'public-key',
                ],
            ])
            ->assertNotFound();
    }

    public function test_dashboard_shows_push_button_when_push_enabled(): void
    {
        config([
            'push.enabled' => true,
            'push.vapid.public_key' => 'BH_test_public_key',
        ]);

        $this->seed(PlatformSeeder::class);

        $user = User::create([
            'name' => 'عضو لوحة',
            'email' => 'dashboard-push@example.com',
            'password' => Hash::make('password123'),
        ]);

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => 'https://push.example.com/subscription/existing',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-push-enable', false)
            ->assertSee('BH_test_public_key', false);
    }
}
