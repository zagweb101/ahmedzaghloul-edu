<?php

namespace App\Models;

use App\Enums\AccessLevel;
use App\Enums\SkillLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LearningPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'cover_image_path',
        'level',
        'access_level',
        'sort_order',
        'is_published',
    ];

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path
            ? asset('storage/' . $this->cover_image_path)
            : null;
    }

    public function seoTitle(): string
    {
        return $this->seo_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        if ($this->seo_description) {
            return $this->seo_description;
        }

        return Str::limit(strip_tags((string) $this->description), 160, '…')
            ?: 'مسار تعليمي عملي في بيت المصور مع دروس وفيديوهات وملفات مساعدة.';
    }

    public function totalDurationMinutes(): int
    {
        return (int) $this->lessons->sum('duration_minutes');
    }

    protected function casts(): array
    {
        return [
            'level' => SkillLevel::class,
            'access_level' => AccessLevel::class,
            'is_published' => 'boolean',
        ];
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }
}
