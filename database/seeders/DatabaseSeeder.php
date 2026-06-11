<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم تجريبي',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير المنصة',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ],
        );

        $this->call(PlatformSeeder::class);

        $monthlyPlan = \App\Models\SubscriptionPlan::where('slug', 'monthly')->first();

        UserSubscription::updateOrCreate(
            [
                'user_id' => $testUser->id,
                'status' => 'active',
            ],
            [
                'subscription_plan_id' => $monthlyPlan?->id,
                'access_level' => \App\Enums\AccessLevel::Member,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ],
        );
    }
}
