<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccessLevel;
use App\Enums\SkillLevel;
use App\Http\Controllers\Controller;
use App\Models\LearningPath;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LearningPathController extends Controller
{
    public function index(): View
    {
        return view('admin.learning-paths.index', [
            'paths' => LearningPath::query()
                ->withCount('lessons')
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.learning-paths.create', [
            'levels' => SkillLevel::cases(),
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function store(Request $request, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', 'unique:learning_paths,slug'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'level' => ['required', Rule::enum(SkillLevel::class)],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['cover_image']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image_path'] = $files->storePublicImage(
                $request->file('cover_image'),
                'learning-paths/covers',
            );
        }

        $validated['is_published'] = $request->boolean('is_published');
        LearningPath::create($validated);

        return redirect()
            ->route('admin.learning-paths.index')
            ->with('status', 'تم إضافة المسار بنجاح.');
    }

    public function edit(LearningPath $learningPath): View
    {
        return view('admin.learning-paths.edit', [
            'path' => $learningPath,
            'levels' => SkillLevel::cases(),
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function update(Request $request, LearningPath $learningPath, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('learning_paths', 'slug')->ignore($learningPath->id)],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'remove_cover_image' => ['nullable', 'boolean'],
            'level' => ['required', Rule::enum(SkillLevel::class)],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['cover_image'], $validated['remove_cover_image']);

        $validated['cover_image_path'] = $files->replacePublicImage(
            $learningPath->cover_image_path,
            $request->file('cover_image'),
            'learning-paths/covers',
            $request->boolean('remove_cover_image'),
        );

        $validated['is_published'] = $request->boolean('is_published');
        $learningPath->update($validated);

        return redirect()
            ->route('admin.learning-paths.index')
            ->with('status', 'تم تحديث المسار بنجاح.');
    }

    public function destroy(LearningPath $learningPath, FileStorageService $files): RedirectResponse
    {
        $learningPath->load('lessons');

        $files->delete($learningPath->cover_image_path);

        foreach ($learningPath->lessons as $lesson) {
            $files->delete($lesson->thumbnail_path);
            $files->delete($lesson->pdf_path, 'local');
        }

        $learningPath->delete();

        return redirect()
            ->route('admin.learning-paths.index')
            ->with('status', 'تم حذف المسار وملفاته.');
    }
}
