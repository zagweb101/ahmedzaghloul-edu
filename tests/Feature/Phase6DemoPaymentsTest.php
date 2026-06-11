<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\PaymentDisplay;
use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase6DemoPaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlatformSeeder::class);
    }

    public function test_demo_mode_shows_demo_bank_details_on_checkout(): void
    {
        config([
            'payments.demo_mode' => true,
            'payments.driver' => 'manual',
        ]);

        $user = User::create([
            'name' => 'مختبر تجريبي',
            'email' => 'demo-bank@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $this->actingAs($user)
            ->get(route('subscription-plans.checkout', $plan))
            ->assertOk()
            ->assertSee('وضع تجريبي')
            ->assertSee(config('payments.demo_bank.iban'))
            ->assertSee('بنك تجريبي');
    }

    public function test_demo_driver_checkout_activates_subscription_immediately(): void
    {
        config(['payments.driver' => 'demo']);

        $user = User::create([
            'name' => 'مشتري تجريبي',
            'email' => 'demo-buyer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = SubscriptionPlan::where('slug', 'yearly')->firstOrFail();

        $this->actingAs($user)
            ->post(route('subscription-plans.checkout.store', $plan))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('subscription_orders', [
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'paid',
            'payment_driver' => 'demo',
        ]);
    }

    public function test_prelaunch_check_skips_iban_warning_when_demo_mode_enabled(): void
    {
        config([
            'payments.demo_mode' => true,
            'payments.manual.iban' => 'SA00 0000 0000 0000 0000 0000',
            'platform.seed_user_emails' => [],
            'app.debug' => false,
            'platform.mail_notifications' => true,
            'mail.mailers.smtp.username' => 'smtp-user',
            'mail.from.address' => 'hello@example.com',
            'seo.google_analytics_id' => 'G-REAL123456',
        ]);

        $this->artisan('platform:prelaunch-check')
            ->assertSuccessful()
            ->expectsOutputToContain('PAYMENT_DEMO_MODE is enabled');
    }

    public function test_demo_mode_banner_appears_on_homepage(): void
    {
        config(['payments.demo_mode' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee('وضع تجريبي');
    }

    public function test_payment_display_detects_stripe_test_keys(): void
    {
        config([
            'payments.driver' => 'stripe',
            'payments.stripe.secret_key' => 'sk_test_abc123',
        ]);

        $this->assertTrue(PaymentDisplay::isStripeTestMode());
        $this->assertSame('Stripe تجريبي', PaymentDisplay::driverLabel());
    }
}
