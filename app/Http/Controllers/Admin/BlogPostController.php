<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog-posts.index', [
            'posts' => BlogPost::query()->latest('published_at')->latest('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.create');
    }

    public function store(Request $request, FileStorageService $files): RedirectResponse
    {
        $validated = $this->validatedData($request);
        unset($validated['cover_image']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image_path'] = $files->storePublicImage(
                $request->file('cover_image'),
                'blog/covers',
            );
        }

        $validated['is_published'] = $request->boolean('is_published');

        BlogPost::create($validated);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'تم إضافة المقال بنجاح.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'post' => $blogPost,
        ]);
    }

    public function update(Request $request, BlogPost $blogPost, FileStorageService $files): RedirectResponse
    {
        $validated = $this->validatedData($request, $blogPost);
        unset($validated['cover_image'], $validated['remove_cover_image']);

        $validated['cover_image_path'] = $files->replacePublicImage(
            $blogPost->cover_image_path,
            $request->file('cover_image'),
            'blog/covers',
            $request->boolean('remove_cover_image'),
        );

        $validated['is_published'] = $request->boolean('is_published');
        $blogPost->update($validated);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'تم تحديث المقال بنجاح.');
    }

    public function destroy(BlogPost $blogPost, FileStorageService $files): RedirectResponse
    {
        $files->delete($blogPost->cover_image_path);
        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'تم حذف المقال.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?BlogPost $blogPost = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($blogPost?->id),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'remove_cover_image' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }
}
