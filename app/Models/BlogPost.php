<?php

namespace App\Models;

use App\Concerns\HasSeo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    use HasSeo;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'author_name',
        'published_at',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function coverImageUrl(): ?string
    {
        return $this->cover_image_path
            ? asset('storage/' . $this->cover_image_path)
            : null;
    }

    public function authorName(): string
    {
        return $this->author_name ?: 'أحمد زغلول';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
