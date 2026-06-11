<?php

namespace Tests\Feature;

use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6BrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_brand_identity_and_testimonials(): void
    {
        $this->seed(PlatformSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('ابدأ رحلتك في')
            ->assertSee('بيت لكل مصور')
            ->assertSee('تعلّم · ألهم · أبدع')
            ->assertSee('شهادات الأعضاء')
            ->assertSee('logo-horizontal.png', false)
            ->assertSee('promo-journey.png', false)
            ->assertSee(config('testimonials.items.0.name'));
    }
}
