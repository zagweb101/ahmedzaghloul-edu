<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommunityController extends Controller
{
    public function index(): View
    {
        return view('admin.community.index', [
            'posts' => CommunityPost::query()
                ->with('user')
                ->withCount(['comments', 'likedByUsers'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function togglePinned(CommunityPost $communityPost): RedirectResponse
    {
        $communityPost->update(['is_pinned' => ! $communityPost->is_pinned]);

        return back()->with('status', $communityPost->is_pinned ? 'تم تثبيت البوست.' : 'تم إلغاء تثبيت البوست.');
    }

    public function togglePublished(CommunityPost $communityPost): RedirectResponse
    {
        $communityPost->update(['is_published' => ! $communityPost->is_published]);

        return back()->with('status', $communityPost->is_published ? 'تم إظهار البوست.' : 'تم إخفاء البوست.');
    }

    public function destroy(CommunityPost $communityPost, FileStorageService $files): RedirectResponse
    {
        $communityPost->load('images');
        $communityPost->deleteAllImages($files);
        $communityPost->delete();

        return back()->with('status', 'تم حذف البوست نهائيًا.');
    }
}
