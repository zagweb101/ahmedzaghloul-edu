<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LiveEvent;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'users' => User::count(),
                'paths' => LearningPath::count(),
                'lessons' => Lesson::count(),
                'events' => LiveEvent::count(),
                'plans' => SubscriptionPlan::count(),
                'posts' => CommunityPost::count(),
            ],
        ]);
    }
}
