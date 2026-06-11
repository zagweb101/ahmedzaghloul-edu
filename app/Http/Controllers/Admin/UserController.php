<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccessLevel;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with(['subscriptions.plan'])
                ->latest()
                ->paginate(20),
            'plans' => SubscriptionPlan::query()
                ->where('is_active', true)
                ->where('slug', '!=', 'free')
                ->orderBy('sort_order')
                ->get(),
            'accessLevels' => [AccessLevel::Member, AccessLevel::Premium],
        ]);
    }

    public function grantSubscription(
        Request $request,
        User $user,
        SubscriptionService $subscriptions,
    ): RedirectResponse {
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'access_level' => ['required', Rule::enum(AccessLevel::class)],
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);
        abort_if($plan->slug === 'free', 422);

        $subscriptions->activate($user, $plan, $validated['access_level']);

        return back()->with('status', "تم تفعيل الاشتراك للمستخدم {$user->name}.");
    }
}
