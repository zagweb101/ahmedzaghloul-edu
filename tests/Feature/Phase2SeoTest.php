<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Phase2SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlatformSeeder::class);
    }

    public function test_live_event_show_page_includes_seo_metadata(): void
    {
        $event = LiveEvent::where('slug', 'first-live')->firstOrFail();

        $this->get(route('live-events.show', $event))
            ->assertOk()
            ->assertSee($event->seoDescription(), false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Event', false)
            ->assertSee(route('live-events.show', $event), false);
    }

    public function test_subscription_plan_show_page_includes_seo_metadata(): void
    {
        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $this->get(route('subscription-plans.show', $plan))
            ->assertOk()
            ->assertSee($plan->seoDescription(), false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Product', false)
            ->assertSee(route('subscription-plans.show', $plan), false);
    }

    public function test_blog_pages_are_available_with_article_schema(): void
    {
        $post = BlogPost::where('slug', 'photography-basics-guide')->firstOrFail();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('المدونة')
            ->assertSee($post->title);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('Article', false)
            ->assertSee(route('blog.show', $post), false);
    }

    public function test_home_page_includes_website_structured_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('WebSite', false);
    }

    public function test_google_analytics_script_is_rendered_when_configured(): void
    {
        config(['seo.google_analytics_id' => 'G-TEST12345']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST12345', false)
            ->assertSee('G-TEST12345', false);
    }

    public function test_private_pages_include_noindex_robots_tag(): void
    {
        $user = User::create([
            'name' => 'عضو خاص',
            'email' => 'private-seo@example.com',
            'password' => Hash::make('password123'),
        ]);

        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();

        $this->actingAs($user)
            ->get(route('subscription-plans.checkout', $plan))
            ->assertOk()
            ->assertSee('noindex, nofollow', false);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('noindex, nofollow', false);
    }

    public function test_sitemap_includes_phase2_urls(): void
    {
        $event = LiveEvent::where('slug', 'first-live')->firstOrFail();
        $plan = SubscriptionPlan::where('slug', 'monthly')->firstOrFail();
        $post = BlogPost::where('slug', 'photography-basics-guide')->firstOrFail();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('live-events.show', $event), false)
            ->assertSee(route('subscription-plans.show', $plan), false)
            ->assertSee(route('blog.show', $post), false)
            ->assertSee(route('blog.index'), false);
    }
}
