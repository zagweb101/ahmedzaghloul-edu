<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class LearningPathController extends Controller
{
    public function index(): View
    {
        return view('learning-paths.index', [
            'paths' => LearningPath::query()
                ->withCount('lessons')
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function show(Request $request, LearningPath $learningPath): View
    {
        abort_unless($learningPath->is_published, 404);

        $path = $learningPath->load(['lessons' => fn ($query) => $query
            ->where('is_published', true)
            ->orderBy('sort_order')]);

        $completedLessonIds = $request->user()
            ? LessonProgress::query()
                ->where('user_id', $request->user()->id)
                ->whereNotNull('completed_at')
                ->whereIn('lesson_id', $path->lessons->pluck('id'))
                ->pluck('lesson_id')
            : collect();

        return view('learning-paths.show', [
            'path' => $path,
            'completedLessonIds' => $completedLessonIds,
        ]);
    }
}
