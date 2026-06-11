<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'learning_path_id',
        'title',
        'slug',
        'summary',
        'thumbnail_path',
        'video_url',
        'pdf_url',
        'pdf_path',
        'duration_minutes',
        'access_level',
        'sort_order',
        'is_published',
    ];

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail_path
            ? asset('storage/' . $this->thumbnail_path)
            : null;
    }

    public function hasPdf(): bool
    {
        return $this->pdf_path !== null || $this->pdf_url !== null;
    }

    protected function casts(): array
    {
        return [
            'access_level' => AccessLevel::class,
            'is_published' => 'boolean',
        ];
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
