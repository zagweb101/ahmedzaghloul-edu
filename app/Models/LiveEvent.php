<?php

namespace App\Models;

use App\Concerns\HasSeo;
use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveEvent extends Model
{
    use HasFactory;
    use HasSeo;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'cover_image_path',
        'starts_at',
        'ends_at',
        'location',
        'capacity',
        'stream_url',
        'access_level',
        'is_published',
    ];

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path
            ? asset('storage/' . $this->cover_image_path)
            : null;
    }

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'access_level' => AccessLevel::class,
            'is_published' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(LiveEventRegistration::class);
    }

    public function seoDescription(?string $fallback = null): string
    {
        if ($this->seo_description) {
            return $this->seo_description;
        }

        $description = \Illuminate\Support\Str::limit(strip_tags((string) $this->description), 160, '…');

        return $description !== ''
            ? $description
            : ($fallback ?: 'لايف تعليمي أو فعالية من بيت المصور مع أحمد زغلول.');
    }

    public function hasAvailableSeats(): bool
    {
        return $this->capacity === null
            || $this->registrations()->where('status', 'registered')->count() < $this->capacity;
    }
}
