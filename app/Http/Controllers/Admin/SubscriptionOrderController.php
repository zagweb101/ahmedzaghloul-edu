<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionOrder;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SubscriptionOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.subscription-orders.index', [
            'orders' => SubscriptionOrder::query()
                ->with(['user', 'plan'])
                ->latest()
                ->paginate(20),
        ]);
    }

    public function approve(SubscriptionOrder $subscriptionOrder, PaymentService $payments): RedirectResponse
    {
        $payments->markPaid($subscriptionOrder);

        return back()->with('status', 'تم تأكيد الدفع وتفعيل الاشتراك.');
    }

    public function cancel(SubscriptionOrder $subscriptionOrder, PaymentService $payments): RedirectResponse
    {
        $payments->cancel($subscriptionOrder);

        return back()->with('status', 'تم إلغاء طلب الاشتراك.');
    }
}
