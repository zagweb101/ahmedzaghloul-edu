<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccessLevel;
use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Models\Lesson;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LessonController extends Controller
{
    public function index(LearningPath $learningPath): View
    {
        return view('admin.lessons.index', [
            'path' => $learningPath,
            'lessons' => $learningPath->lessons()->get(),
        ]);
    }

    public function create(LearningPath $learningPath): View
    {
        return view('admin.lessons.create', [
            'path' => $learningPath,
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function store(Request $request, LearningPath $learningPath, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:255',
                Rule::unique('lessons', 'slug')->where('learning_path_id', $learningPath->id),
            ],
            'summary' => ['nullable', 'string'],
            'thumbnail' => FileStorageService::IMAGE_RULES,
            'video_url' => ['nullable', 'url', 'max:255'],
            'pdf_url' => ['nullable', 'url', 'max:255'],
            'pdf_file' => FileStorageService::PDF_RULES,
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['thumbnail'], $validated['pdf_file']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail_path'] = $files->storePublicImage(
                $request->file('thumbnail'),
                'lessons/thumbnails',
            );
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_path'] = $files->storePrivateDocument(
                $request->file('pdf_file'),
                'lessons/pdfs',
            );
        }

        $validated['is_published'] = $request->boolean('is_published');
        $learningPath->lessons()->create($validated);

        return redirect()
            ->route('admin.learning-paths.lessons.index', $learningPath)
            ->with('status', 'تم إضافة الدرس بنجاح.');
    }

    public function edit(LearningPath $learningPath, Lesson $lesson): View
    {
        abort_unless($lesson->learning_path_id === $learningPath->id, 404);

        return view('admin.lessons.edit', [
            'path' => $learningPath,
            'lesson' => $lesson,
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function update(
        Request $request,
        LearningPath $learningPath,
        Lesson $lesson,
        FileStorageService $files,
    ): RedirectResponse {
        abort_unless($lesson->learning_path_id === $learningPath->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:255',
                Rule::unique('lessons', 'slug')
                    ->where('learning_path_id', $learningPath->id)
                    ->ignore($lesson->id),
            ],
            'summary' => ['nullable', 'string'],
            'thumbnail' => FileStorageService::IMAGE_RULES,
            'remove_thumbnail' => ['nullable', 'boolean'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'pdf_url' => ['nullable', 'url', 'max:255'],
            'pdf_file' => FileStorageService::PDF_RULES,
            'remove_pdf_file' => ['nullable', 'boolean'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['thumbnail'], $validated['pdf_file'], $validated['remove_thumbnail'], $validated['remove_pdf_file']);

        $validated['thumbnail_path'] = $files->replacePublicImage(
            $lesson->thumbnail_path,
            $request->file('thumbnail'),
            'lessons/thumbnails',
            $request->boolean('remove_thumbnail'),
        );

        $validated['pdf_path'] = $files->replacePrivateDocument(
            $lesson->pdf_path,
            $request->file('pdf_file'),
            'lessons/pdfs',
            $request->boolean('remove_pdf_file'),
        );

        $validated['is_published'] = $request->boolean('is_published');
        $lesson->update($validated);

        return redirect()
            ->route('admin.learning-paths.lessons.index', $learningPath)
            ->with('status', 'تم تحديث الدرس بنجاح.');
    }

    public function destroy(
        LearningPath $learningPath,
        Lesson $lesson,
        FileStorageService $files,
    ): RedirectResponse {
        abort_unless($lesson->learning_path_id === $learningPath->id, 404);

        $files->delete($lesson->thumbnail_path);
        $files->delete($lesson->pdf_path, 'local');
        $lesson->delete();

        return redirect()
            ->route('admin.learning-paths.lessons.index', $learningPath)
            ->with('status', 'تم حذف الدرس وملفاته.');
    }
}
