<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityPostImage;
use App\Services\CommunityNotifier;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CommunityController extends Controller
{
    public function index(): View
    {
        $postsQuery = CommunityPost::query()
            ->with([
                'user',
                'images',
                'comments' => fn ($query) => $query->where('is_published', true)->with('user'),
            ])
            ->withCount('likedByUsers')
            ->where('is_published', true)
            ->orderByDesc('is_pinned')
            ->latest();

        if (auth()->check()) {
            $postsQuery->withExists([
                'likedByUsers as liked_by_current_user' => fn ($query) => $query->whereKey(auth()->id()),
            ]);
        }

        return view('community.index', [
            'posts' => $postsQuery->take(12)->get(),
        ]);
    }

    public function gallery(Request $request): View
    {
        $category = $request->query('category');

        $imagesQuery = CommunityPostImage::query()
            ->with([
                'post' => fn ($query) => $query
                    ->with('user')
                    ->withCount('likedByUsers')
                    ->with('images'),
            ])
            ->whereHas('post', function ($query) use ($category) {
                $query->where('is_published', true);

                if (in_array($category, ['question', 'showcase', 'challenge', 'feedback', 'gear'], true)) {
                    $query->where('category', $category);
                }
            })
            ->latest();

        $images = $imagesQuery->paginate(48)->withQueryString();

        return view('community.gallery', [
            'images' => $images,
            'activeCategory' => $category,
        ]);
    }

    public function store(Request $request, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(['question', 'showcase', 'challenge', 'feedback', 'gear'])],
            'images' => FileStorageService::POST_IMAGES_RULES,
            'images.*' => FileStorageService::POST_IMAGE_ITEM_RULES,
            'image' => FileStorageService::IMAGE_RULES,
        ]);

        $uploads = $this->uploadedImages($request);

        if (count($uploads) > FileStorageService::MAX_POST_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'يمكنك رفع ' . FileStorageService::MAX_POST_IMAGES . ' صور كحد أقصى.',
            ]);
        }

        unset($validated['images'], $validated['image']);

        $post = $request->user()->communityPosts()->create($validated);
        $this->attachImages($post, $uploads, $files);

        return redirect()
            ->route('community.index')
            ->with('status', 'تم نشر البوست بنجاح.');
    }

    public function edit(Request $request, CommunityPost $communityPost): View
    {
        abort_unless($communityPost->canBeEditedBy($request->user()), 403);

        return view('community.edit', [
            'post' => $communityPost->load('images'),
        ]);
    }

    public function update(Request $request, CommunityPost $communityPost, FileStorageService $files): RedirectResponse
    {
        abort_unless($communityPost->canBeEditedBy($request->user()), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(['question', 'showcase', 'challenge', 'feedback', 'gear'])],
            'images' => FileStorageService::POST_IMAGES_RULES,
            'images.*' => FileStorageService::POST_IMAGE_ITEM_RULES,
            'remove_image_ids' => ['nullable', 'array'],
            'remove_image_ids.*' => ['integer'],
        ]);

        $removeIds = collect($validated['remove_image_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $imagesToRemove = $communityPost->images()
            ->whereIn('id', $removeIds)
            ->get();

        $remainingCount = $communityPost->images()->whereNotIn('id', $removeIds)->count();
        $newUploads = $this->uploadedImages($request);

        if ($remainingCount + count($newUploads) > FileStorageService::MAX_POST_IMAGES) {
            throw ValidationException::withMessages([
                'images' => 'يمكنك الاحتفاظ بـ ' . FileStorageService::MAX_POST_IMAGES . ' صور كحد أقصى.',
            ]);
        }

        unset($validated['images'], $validated['remove_image_ids']);

        $communityPost->update($validated);

        foreach ($imagesToRemove as $image) {
            $files->delete($image->image_path);
            $image->delete();
        }

        $this->attachImages($communityPost, $newUploads, $files);
        $communityPost->syncCoverImage();

        return redirect()
            ->route('community.index')
            ->with('status', 'تم تحديث البوست بنجاح.');
    }

    public function comment(
        Request $request,
        CommunityPost $communityPost,
        FileStorageService $files,
        CommunityNotifier $notifier,
    ): RedirectResponse {
        abort_unless($communityPost->is_published, 404);

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:2000', 'required_without:image'],
            'image' => FileStorageService::IMAGE_RULES,
        ]);

        unset($validated['image']);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $files->storePublicImage(
                $request->file('image'),
                'community/comments',
            );
        }

        $communityPost->comments()->create([
            'user_id' => $request->user()->id,
            ...$validated,
        ]);

        $notifier->comment(
            $communityPost->loadMissing('user'),
            $request->user(),
            $validated['body'] ?? null,
        );

        return redirect()
            ->route('community.index')
            ->with('status', 'تم إضافة التعليق.');
    }

    public function toggleLike(
        Request $request,
        CommunityPost $communityPost,
        CommunityNotifier $notifier,
    ): RedirectResponse {
        abort_unless($communityPost->is_published, 404);

        $result = $request->user()->likedCommunityPosts()->toggle($communityPost->id);

        if (! empty($result['attached'])) {
            $notifier->like($communityPost->loadMissing('user'), $request->user());
        }

        return back();
    }

    public function destroy(Request $request, CommunityPost $communityPost, FileStorageService $files): RedirectResponse
    {
        abort_unless($communityPost->canBeEditedBy($request->user()), 403);

        $communityPost->load('images');
        $communityPost->deleteAllImages($files);
        $communityPost->delete();

        return redirect()
            ->route('community.index')
            ->with('status', 'تم حذف البوست.');
    }

    /**
     * @return list<\Illuminate\Http\UploadedFile>
     */
    private function uploadedImages(Request $request): array
    {
        $files = $request->file('images', []);

        if ($request->hasFile('image')) {
            $files[] = $request->file('image');
        }

        return array_values(array_filter($files));
    }

    /**
     * @param  list<\Illuminate\Http\UploadedFile>  $uploads
     */
    private function attachImages(CommunityPost $post, array $uploads, FileStorageService $files): void
    {
        if ($uploads === []) {
            $post->syncCoverImage();

            return;
        }

        $paths = $files->storePublicImages($uploads, 'community');
        $nextOrder = (int) $post->images()->max('sort_order');

        foreach ($paths as $path) {
            $post->images()->create([
                'image_path' => $path,
                'sort_order' => ++$nextOrder,
            ]);
        }

        $post->syncCoverImage();
    }
}
