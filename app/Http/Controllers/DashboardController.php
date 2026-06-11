<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LiveEvent;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('subscriptions.plan');
        $accessibleLessons = Lesson::query()
            ->with('learningPath')
            ->where('is_published', true)
            ->orderBy('learning_path_id')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Lesson $lesson) => $user->canAccess($lesson->access_level));

        $completedLessonIds = LessonProgress::query()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');

        $completedCount = $accessibleLessons->whereIn('id', $completedLessonIds)->count();
        $progressPercentage = $accessibleLessons->isEmpty()
            ? 0
            : (int) round(($completedCount / $accessibleLessons->count()) * 100);

        return view('dashboard.index', [
            'recommendedPath' => LearningPath::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->first(),
            'upcomingLive' => LiveEvent::query()
                ->where('is_published', true)
                ->orderByRaw('starts_at is null')
                ->orderBy('starts_at')
                ->first(),
            'activeSubscription' => $user->activeSubscription(),
            'continueLesson' => $accessibleLessons
                ->first(fn (Lesson $lesson) => ! $completedLessonIds->contains($lesson->id)),
            'completedCount' => $completedCount,
            'accessibleLessonsCount' => $accessibleLessons->count(),
            'progressPercentage' => $progressPercentage,
            'registeredEvent' => $user->liveEventRegistrations()
                ->with('event')
                ->where('status', 'registered')
                ->whereHas('event', fn ($query) => $query
                    ->where('is_published', true)
                    ->where(fn ($dateQuery) => $dateQuery
                        ->whereNull('starts_at')
                        ->orWhere('starts_at', '>=', now())))
                ->latest()
                ->first(),
            'recentNotifications' => $user->notifications()->latest()->take(3)->get(),
            'unreadNotificationsCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function updateAvatar(Request $request, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $files->delete($user->avatar_path);

        $user->update([
            'avatar_path' => $files->storePublicImage(
                $validated['avatar'],
                'avatars',
            ),
        ]);

        return back()->with('status', 'تم تحديث صورة الملف الشخصي.');
    }
}
