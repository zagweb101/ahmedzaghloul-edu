<?php

namespace App\Models;

use App\Services\FileStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'category',
        'image_path',
        'is_pinned',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CommunityPostImage::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CommunityComment::class);
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'community_post_likes')->withTimestamps();
    }

    public function imageUrl(): ?string
    {
        $firstImage = $this->relationLoaded('images')
            ? $this->images->first()
            : $this->images()->orderBy('sort_order')->first();

        return $firstImage?->url()
            ?? ($this->image_path ? asset('storage/' . $this->image_path) : null);
    }

    /** @return Collection<int, string> */
    public function imageUrls(): Collection
    {
        $images = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->orderBy('sort_order')->get();

        if ($images->isNotEmpty()) {
            return $images->map(fn (CommunityPostImage $image) => $image->url());
        }

        if ($this->image_path) {
            return collect([asset('storage/' . $this->image_path)]);
        }

        return collect();
    }

    public function hasImages(): bool
    {
        return $this->images->isNotEmpty() || $this->image_path !== null;
    }

    public function syncCoverImage(): void
    {
        $this->updateQuietly([
            'image_path' => $this->images()->orderBy('sort_order')->value('image_path'),
        ]);
    }

    public function deleteAllImages(FileStorageService $files): void
    {
        foreach ($this->images as $image) {
            $files->delete($image->image_path);
        }

        $files->delete($this->image_path);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->is_admin || $this->user_id === $user->id;
    }
}
