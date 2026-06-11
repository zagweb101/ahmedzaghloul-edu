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
            ->assertSee('logo-mark.svg', false)
            ->assertSee('BAYT ALMOSWER')
            ->assertSee('promo-camera-hero.png', false)
            ->assertSee(config('testimonials.items.0.name'));
    }
}
