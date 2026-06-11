<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'is_admin',
    ];

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? asset('storage/' . $this->avatar_path)
            : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function communityPosts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function communityComments(): HasMany
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function likedCommunityPosts(): BelongsToMany
    {
        return $this->belongsToMany(CommunityPost::class, 'community_post_likes')->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function subscriptionOrders(): HasMany
    {
        return $this->hasMany(SubscriptionOrder::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function liveEventRegistrations(): HasMany
    {
        return $this->hasMany(LiveEventRegistration::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function activeSubscription(): ?UserSubscription
    {
        return $this->subscriptions
            ->filter(fn (UserSubscription $subscription) => $subscription->isActive())
            ->sortByDesc(fn (UserSubscription $subscription) => $this->accessRank($subscription->access_level))
            ->first();
    }

    public function canAccess(\App\Enums\AccessLevel $requiredLevel): bool
    {
        if ($requiredLevel === \App\Enums\AccessLevel::Free || $this->is_admin) {
            return true;
        }

        $subscription = $this->activeSubscription();

        return $subscription !== null
            && $this->accessRank($subscription->access_level) >= $this->accessRank($requiredLevel);
    }

    private function accessRank(\App\Enums\AccessLevel $accessLevel): int
    {
        return match ($accessLevel) {
            \App\Enums\AccessLevel::Free => 0,
            \App\Enums\AccessLevel::Member => 1,
            \App\Enums\AccessLevel::Premium => 2,
        };
    }
}
