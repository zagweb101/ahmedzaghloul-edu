<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPostImage extends Model
{
    protected $fillable = [
        'community_post_id',
        'image_path',
        'sort_order',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'community_post_id');
    }

    public function url(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
