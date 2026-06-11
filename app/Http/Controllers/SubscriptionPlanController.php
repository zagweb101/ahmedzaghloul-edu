<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Contracts\View\View;

class SubscriptionPlanController extends Controller
{
    public function index(): View
    {
        return view('subscription-plans.index', [
            'plans' => $this->activePlansQuery()->get(),
        ]);
    }

    public function show(SubscriptionPlan $subscriptionPlan): View
    {
        abort_unless($subscriptionPlan->is_active, 404);

        return view('subscription-plans.show', [
            'plan' => $subscriptionPlan,
        ]);
    }

    private function activePlansQuery()
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
