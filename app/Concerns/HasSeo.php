<?php

namespace App\Concerns;

use Illuminate\Support\Str;

trait HasSeo
{
    public function seoTitle(): string
    {
        if (! empty($this->seo_title)) {
            return $this->seo_title;
        }

        return (string) ($this->title ?? $this->name ?? '');
    }

    public function seoDescription(?string $fallback = null): string
    {
        if (! empty($this->seo_description)) {
            return $this->seo_description;
        }

        $source = $this->description ?? $this->excerpt ?? $this->summary ?? '';

        $description = Str::limit(strip_tags((string) $source), 160, '…');

        return $description !== '' ? $description : (string) ($fallback ?? '');
    }
}
