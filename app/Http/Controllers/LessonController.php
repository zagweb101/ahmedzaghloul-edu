<?php

namespace App\Http\Controllers;

use App\Enums\AccessLevel;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LessonController extends Controller
{
    public function show(Request $request, LearningPath $learningPath, Lesson $lesson): View
    {
        abort_unless(
            $learningPath->is_published
            && $lesson->is_published
            && $lesson->learning_path_id === $learningPath->id,
            404,
        );

        $canAccess = $this->canAccess($request, $lesson->access_level);
        $progress = null;

        if ($request->user() && $canAccess) {
            $progress = LessonProgress::firstOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'lesson_id' => $lesson->id,
                ],
                ['started_at' => now()],
            );
        }

        return view('lessons.show', [
            'path' => $learningPath,
            'lesson' => $lesson,
            'canAccess' => $canAccess,
            'progress' => $progress,
            'nextLesson' => $learningPath->lessons()
                ->where('is_published', true)
                ->where('sort_order', '>', $lesson->sort_order)
                ->orderBy('sort_order')
                ->first(),
        ]);
    }

    public function complete(Request $request, LearningPath $learningPath, Lesson $lesson): RedirectResponse
    {
        abort_unless(
            $lesson->learning_path_id === $learningPath->id
            && $lesson->is_published
            && $request->user()->canAccess($lesson->access_level),
            403,
        );

        LessonProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'started_at' => now(),
                'completed_at' => now(),
            ],
        );

        return back()->with('status', 'تم تسجيل الدرس كمكتمل.');
    }

    public function downloadPdf(Request $request, LearningPath $learningPath, Lesson $lesson): StreamedResponse
    {
        abort_unless(
            $learningPath->is_published
            && $lesson->is_published
            && $lesson->learning_path_id === $learningPath->id
            && $lesson->pdf_path,
            404,
        );

        abort_unless($this->canAccess($request, $lesson->access_level), 403);

        return Storage::disk('local')->download(
            $lesson->pdf_path,
            $lesson->slug . '.pdf',
        );
    }

    private function canAccess(Request $request, AccessLevel $requiredLevel): bool
    {
        if ($requiredLevel === AccessLevel::Free) {
            return true;
        }

        return $request->user()?->canAccess($requiredLevel) ?? false;
    }
}
