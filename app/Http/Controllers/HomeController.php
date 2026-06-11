<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'paths' => LearningPath::query()
                ->withCount('lessons')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(),
            'plans' => SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'upcomingLive' => LiveEvent::query()
                ->where('is_published', true)
                ->orderByRaw('starts_at is null')
                ->orderBy('starts_at')
                ->first(),
        ]);
    }
}
