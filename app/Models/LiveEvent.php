<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
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

    public function hasAvailableSeats(): bool
    {
        return $this->capacity === null
            || $this->registrations()->where('status', 'registered')->count() < $this->capacity;
    }
}
