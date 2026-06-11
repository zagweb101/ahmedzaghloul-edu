<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_cents',
        'currency',
        'billing_period',
        'description',
        'features',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SubscriptionOrder::class);
    }

    public function formattedPrice(): string
    {
        if ($this->price_cents <= 0) {
            return $this->slug === 'free' ? '0 ريال' : 'يحدد لاحقًا';
        }

        return number_format($this->price_cents / 100) . ' ' . $this->currency;
    }

    public function isFree(): bool
    {
        return $this->slug === 'free' || $this->billing_period === 'free';
    }

    public function defaultAccessLevel(): AccessLevel
    {
        return match ($this->slug) {
            'yearly' => AccessLevel::Premium,
            default => AccessLevel::Member,
        };
    }
}
