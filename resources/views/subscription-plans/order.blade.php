@extends('layouts.app')

@section('title', 'طلب ' . $order->reference)

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-7">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <article class="surface-card p-4">
                    <span class="badge badge-soft mb-3">طلب اشتراك</span>
                    <h1 class="h3 mb-2">{{ $order->reference }}</h1>
                    <p class="text-muted-soft mb-4">
                        الخطة: {{ $order->plan?->name }} · {{ $order->formattedAmount() }}
                    </p>

                    @if ($order->isPaid())
                        <div class="alert alert-success mb-0">
                            تم تفعيل اشتراكك بنجاح. يمكنك البدء من لوحة العضو.
                        </div>
                        <a class="btn btn-brand mt-3" href="{{ route('dashboard') }}">الذهاب إلى لوحتي</a>
                    @elseif ($order->isPending())
                        @if ($order->checkout_url && in_array($order->payment_driver, ['tap', 'stripe']))
                            <div class="alert alert-warning mb-0">
                                أكمل الدفع عبر {{ $order->payment_driver === 'stripe' ? 'Stripe' : 'Tap' }} لتفعيل اشتراكك.
                                @if ($order->payment_driver === 'stripe' && \App\Support\PaymentDisplay::isStripeTestMode())
                                    <br><small>بطاقة تجريبية: 4242 4242 4242 4242</small>
                                @endif
                            </div>
                            <a class="btn btn-brand mt-3" href="{{ $order->checkout_url }}" target="_blank" rel="noopener">
                                إكمال الدفع الآن
                            </a>
                        @else
                            <div class="alert alert-warning mb-0">
                                طلبك قيد المراجعة. بعد تأكيد التحويل سيتم تفعيل الاشتراك تلقائيًا.
                            </div>
                        @endif
                        @if ($order->customer_note)
                            <p class="text-muted-soft mt-3 mb-0">ملاحظتك: {{ $order->customer_note }}</p>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-0">هذا الطلب لم يعد نشطًا.</div>
                    @endif
                </article>
            </div>
        </div>
    </section>
@endsection
