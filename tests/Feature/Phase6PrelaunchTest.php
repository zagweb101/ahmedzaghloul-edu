<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PlatformSeeder;
use Database\Seeders\ProductionContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase6PrelaunchTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_content_seeder_creates_blog_and_seo_data(): void
    {
        $this->seed(PlatformSeeder::class);
        $this->seed(ProductionContentSeeder::class);

        $this->assertDatabaseCount('blog_posts', 3);
        $this->assertDatabaseHas('subscription_plans', [
            'slug' => 'monthly',
            'seo_title' => 'الاشتراك الشهري — بيت المصور',
        ]);
        $this->assertDatabaseHas('live_events', [
            'slug' => 'first-live',
        ]);

        $event = \App\Models\LiveEvent::where('slug', 'first-live')->firstOrFail();
        $this->assertNotNull($event->stream_url);
    }

    public function test_prelaunch_check_warns_about_seed_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->artisan('platform:prelaunch-check')
            ->assertFailed()
            ->expectsOutputToContain('Seed account still exists: admin@example.com');
    }

    public function test_remove_seed_users_command_deletes_demo_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->artisan('platform:remove-seed-users', ['--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_rotate_admin_password_updates_admin_credentials(): void
    {
        $admin = User::create([
            'name' => 'مدير الإنتاج',
            'email' => 'owner@baytalmosawer.net',
            'password' => Hash::make('old-password-123'),
            'is_admin' => true,
        ]);

        $this->artisan('platform:rotate-admin-password', [
            'email' => 'owner@baytalmosawer.net',
            '--password' => 'new-secure-password-99',
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('new-secure-password-99', $admin->fresh()->password));
    }
}
