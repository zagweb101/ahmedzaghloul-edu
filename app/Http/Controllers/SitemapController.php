<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\LiveEvent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('learning-paths.index'), 'priority' => '0.9'],
            ['loc' => route('subscription-plans.index'), 'priority' => '0.9'],
            ['loc' => route('live-events.index'), 'priority' => '0.8'],
            ['loc' => route('community.index'), 'priority' => '0.8'],
            ['loc' => route('community.gallery'), 'priority' => '0.7'],
        ]);

        LearningPath::query()
            ->where('is_published', true)
            ->get(['slug'])
            ->each(fn ($path) => $urls->push([
                'loc' => route('learning-paths.show', $path),
                'priority' => '0.8',
            ]));

        LiveEvent::query()
            ->where('is_published', true)
            ->get(['id'])
            ->each(fn ($event) => $urls->push([
                'loc' => route('live-events.index') . '#event-' . $event->id,
                'priority' => '0.6',
            ]));

        $xml = view('sitemap', [
            'urls' => $urls,
            'lastmod' => now()->toAtomString(),
        ])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
