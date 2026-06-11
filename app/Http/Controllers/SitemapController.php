<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'lastmod' => now()->toAtomString()],
            ['loc' => route('learning-paths.index'), 'priority' => '0.9', 'lastmod' => now()->toAtomString()],
            ['loc' => route('subscription-plans.index'), 'priority' => '0.9', 'lastmod' => now()->toAtomString()],
            ['loc' => route('live-events.index'), 'priority' => '0.8', 'lastmod' => now()->toAtomString()],
            ['loc' => route('blog.index'), 'priority' => '0.8', 'lastmod' => now()->toAtomString()],
            ['loc' => route('community.index'), 'priority' => '0.8', 'lastmod' => now()->toAtomString()],
            ['loc' => route('community.gallery'), 'priority' => '0.7', 'lastmod' => now()->toAtomString()],
        ]);

        SubscriptionPlan::query()
            ->where('is_active', true)
            ->get(['slug', 'updated_at'])
            ->each(fn ($plan) => $urls->push([
                'loc' => route('subscription-plans.show', $plan),
                'priority' => '0.8',
                'lastmod' => $plan->updated_at?->toAtomString() ?? now()->toAtomString(),
            ]));

        LearningPath::query()
            ->where('is_published', true)
            ->get(['id', 'slug', 'updated_at'])
            ->each(function ($path) use ($urls) {
                $urls->push([
                    'loc' => route('learning-paths.show', $path),
                    'priority' => '0.8',
                    'lastmod' => $path->updated_at?->toAtomString() ?? now()->toAtomString(),
                ]);

                Lesson::query()
                    ->where('learning_path_id', $path->id)
                    ->where('is_published', true)
                    ->orderBy('sort_order')
                    ->get(['slug', 'updated_at'])
                    ->each(fn ($lesson) => $urls->push([
                        'loc' => route('lessons.show', [$path, $lesson]),
                        'priority' => '0.7',
                        'lastmod' => $lesson->updated_at?->toAtomString() ?? now()->toAtomString(),
                    ]));
            });

        LiveEvent::query()
            ->where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->each(fn ($event) => $urls->push([
                'loc' => route('live-events.show', $event),
                'priority' => '0.7',
                'lastmod' => $event->updated_at?->toAtomString() ?? now()->toAtomString(),
            ]));

        BlogPost::query()
            ->published()
            ->get(['slug', 'updated_at', 'published_at'])
            ->each(fn ($post) => $urls->push([
                'loc' => route('blog.show', $post),
                'priority' => '0.7',
                'lastmod' => ($post->updated_at ?? $post->published_at)?->toAtomString() ?? now()->toAtomString(),
            ]));

        $xml = view('sitemap', [
            'urls' => $urls,
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
