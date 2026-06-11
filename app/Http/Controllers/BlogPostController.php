<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('blog.index', [
            'posts' => BlogPost::query()
                ->published()
                ->latest('published_at')
                ->get(),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless(
            $blogPost->is_published
                && $blogPost->published_at
                && $blogPost->published_at->lte(now()),
            404,
        );

        return view('blog.show', [
            'post' => $blogPost,
        ]);
    }
}
