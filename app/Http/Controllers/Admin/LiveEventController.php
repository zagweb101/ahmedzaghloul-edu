<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccessLevel;
use App\Http\Controllers\Controller;
use App\Models\LiveEvent;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LiveEventController extends Controller
{
    public function index(): View
    {
        return view('admin.live-events.index', [
            'events' => LiveEvent::query()
                ->withCount(['registrations' => fn ($query) => $query->where('status', 'registered')])
                ->latest('starts_at')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.live-events.create', [
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function store(Request $request, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', 'unique:live_events,slug'],
            'description' => ['nullable', 'string'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'stream_url' => ['nullable', 'url', 'max:255'],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['cover_image']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image_path'] = $files->storePublicImage(
                $request->file('cover_image'),
                'live-events/covers',
            );
        }

        $validated['is_published'] = $request->boolean('is_published');
        LiveEvent::create($validated);

        return redirect()
            ->route('admin.live-events.index')
            ->with('status', 'تم إضافة اللايف بنجاح.');
    }

    public function edit(LiveEvent $liveEvent): View
    {
        return view('admin.live-events.edit', [
            'event' => $liveEvent,
            'accessLevels' => AccessLevel::cases(),
        ]);
    }

    public function update(Request $request, LiveEvent $liveEvent, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('live_events', 'slug')->ignore($liveEvent->id)],
            'description' => ['nullable', 'string'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'remove_cover_image' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'stream_url' => ['nullable', 'url', 'max:255'],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
            'is_published' => ['nullable', 'boolean'],
        ]);

        unset($validated['cover_image'], $validated['remove_cover_image']);

        $validated['cover_image_path'] = $files->replacePublicImage(
            $liveEvent->cover_image_path,
            $request->file('cover_image'),
            'live-events/covers',
            $request->boolean('remove_cover_image'),
        );

        $validated['is_published'] = $request->boolean('is_published');
        $liveEvent->update($validated);

        return redirect()
            ->route('admin.live-events.index')
            ->with('status', 'تم تحديث اللايف بنجاح.');
    }

    public function destroy(LiveEvent $liveEvent, FileStorageService $files): RedirectResponse
    {
        $files->delete($liveEvent->cover_image_path);
        $liveEvent->delete();

        return redirect()
            ->route('admin.live-events.index')
            ->with('status', 'تم حذف اللايف وملفاته.');
    }
}
