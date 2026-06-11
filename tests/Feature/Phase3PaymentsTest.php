<?php

namespace Tests\Feature;

use App\Models\LiveEvent;
use App\Models\LiveEventRegistration;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Phase3PaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlatformSeeder::class);
    }

    public function test_subscriptions_process_command_expires_and_notifies(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'مشترك منتهي',
            'email' => 'expired-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'access_level' => 'member',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
        ]);

        $expiringUser = User::create([
            'name' => 'مشترك قريب الانتهاء',
            'email' => 'expiring-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        UserSubscription::create([
            'user_id' => $expiringUser->id,
            'subscription_plan_id' => $plan->id,
            'access_level' => 'member',
            'status' => 'active',
            'starts_at' => now()->subDays(20),
            'ends_at' => now()->addDays(3),
        ]);

        $this->artisan('subscriptions:process')->assertSuccessful();

        $this->assertDatabaseHas('user_subscriptions', [
            'user_id' => $user->id,
            'status' => 'expired',
        ]);

        Notification::assertSentTo($user, SubscriptionExpiredNotification::class);
        Notification::assertSentTo($expiringUser, SubscriptionExpiringNotification::class);
    }

    public function test_registered_user_can_see_live_stream_during_event_window(): void
    {
        $user = User::create([
            'name' => 'حاضر البث',
            'email' => 'stream-member@example.com',
            'password' => Hash::make('password123'),
        ]);

        $event = LiveEvent::where('slug', 'first-live')->firstOrFail();
        $event->update([
            'stream_url' => 'https://www.youtube.com/embed/live-stream',
            'starts_at' => now()->subMinutes(5),
            'ends_at' => now()->addHour(),
        ]);

        LiveEventRegistration::create([
            'live_event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);

        $this->actingAs($user)
            ->get(route('live-events.show', $event))
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/live-stream', false);
    }

    public function test_stripe_payment_driver_redirects_to_checkout_url(): void
    {
        config([
            'payments.driver' => 'stripe',
            'payments.stripe.secret_key' => 'sk_test_fake',
            'payments.stripe.api_url' => 'https://api.stripe.com/v1',
        ]);

        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test123',
                'url' => 'https://checkout.stripe.com/c/pay/cs_test123',
            ], 200),
        ]);

        $member = User::create([
            'name' => 'مشترك سترايب',
            'email' => 'stripe-checkout@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($member)
            ->post('/subscription-plans/monthly/checkout')
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test123');

        $order = \App\Models\SubscriptionOrder::where('user_id', $member->id)->firstOrFail();
        $this->assertSame('cs_test123', $order->gateway_charge_id);
        $this->assertSame('stripe', $order->payment_driver);
    }

    public function test_stripe_webhook_marks_order_as_paid(): void
    {
        Notification::fake();

        $member = User::create([
            'name' => 'مشترك ويبهوك سترايب',
            'email' => 'stripe-webhook@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $order = \App\Models\SubscriptionOrder::create([
            'user_id' => $member->id,
            'subscription_plan_id' => $plan->id,
            'reference' => 'ORD-STRIPE1',
            'amount_cents' => $plan->price_cents,
            'currency' => $plan->currency,
            'status' => 'pending',
            'payment_driver' => 'stripe',
            'gateway_charge_id' => 'cs_webhook1',
        ]);

        $this->postJson('/payments/stripe/webhook', [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_webhook1',
                    'payment_status' => 'paid',
                    'metadata' => [
                        'order_id' => (string) $order->id,
                    ],
                ],
            ],
        ])->assertOk();

        $this->assertTrue($order->fresh()->isPaid());
        $this->assertTrue($member->fresh()->canAccess(\App\Enums\AccessLevel::Member));
    }

    public function test_subscriptions_process_is_scheduled(): void
    {
        $this->artisan('schedule:list')
            ->assertSuccessful()
            ->expectsOutputToContain('subscriptions:process');
    }
}
