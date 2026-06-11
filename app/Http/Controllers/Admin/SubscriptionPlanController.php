<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\FileStorageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        return view('admin.subscription-plans.index', [
            'plans' => SubscriptionPlan::query()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function edit(SubscriptionPlan $subscriptionPlan): View
    {
        return view('admin.subscription-plans.edit', [
            'plan' => $subscriptionPlan,
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan, FileStorageService $files): RedirectResponse
    {
        $validated = $request->validate([
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'cover_image' => FileStorageService::IMAGE_RULES,
            'remove_cover_image' => ['nullable', 'boolean'],
        ]);

        unset($validated['cover_image'], $validated['remove_cover_image']);

        $validated['cover_image_path'] = $files->replacePublicImage(
            $subscriptionPlan->cover_image_path,
            $request->file('cover_image'),
            'subscription-plans/covers',
            $request->boolean('remove_cover_image'),
        );

        $subscriptionPlan->update($validated);

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('status', 'تم تحديث إعدادات SEO للخطة.');
    }
}
