@extends('layouts.app')

@section('title', 'اشتراك ' . $plan->name)

@section('meta_description', 'إتمام اشتراك ' . $plan->name . ' في منصة بيت المصور.')

@php
    $bank = \App\Support\PaymentDisplay::bankDetails();
    $driver = config('payments.driver');
@endphp

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="col-lg-7">
                <p class="fw-bold text-accent mb-2">الدفع</p>
                <h1 class="display-6 fw-bold mb-4">إتمام اشتراك {{ $plan->name }}</h1>

                @if (config('payments.demo_mode'))
                    <div class="alert alert-warning">
                        <strong>وضع تجريبي:</strong> المشتريات هنا للاختبار فقط قبل الإطلاق التجاري.
                    </div>
                @endif

                <article class="surface-card p-4 mb-4">
                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <strong class="fs-4">{{ $plan->formattedPrice() }}</strong>
                        <span class="text-muted-soft">{{ $plan->billing_period === 'year' ? 'سنوي' : 'شهري' }}</span>
                    </div>
                    <p class="text-muted-soft mb-0">{{ $plan->description }}</p>
                </article>

                @if ($driver === 'demo')
                    <article class="surface-card p-4 mb-4">
                        <h2 class="h5 mb-3">تفعيل فوري (تجريبي)</h2>
                        <p class="text-muted-soft mb-0">
                            عند الضغط على الزر سيتم تفعيل الاشتراك مباشرة بدون دفع حقيقي — مناسب لاختبار المسارات واللايفات والمجتمع.
                        </p>
                    </article>
                @elseif ($driver === 'stripe')
                    <article class="surface-card p-4 mb-4">
                        <h2 class="h5 mb-3">Stripe {{ \App\Support\PaymentDisplay::isStripeTestMode() ? '(وضع تجريبي)' : '' }}</h2>
                        <p class="text-muted-soft mb-2">بعد إنشاء الطلب سيتم تحويلك لصفحة دفع Stripe.</p>
                        @if (\App\Support\PaymentDisplay::isStripeTestMode())
                            <p class="text-muted-soft mb-0">
                                بطاقة تجريبية: <code>4242 4242 4242 4242</code> · أي تاريخ مستقبلي · أي CVC
                            </p>
                        @endif
                    </article>
                @elseif ($driver === 'tap')
                    <article class="surface-card p-4 mb-4">
                        <h2 class="h5 mb-3">الدفع الإلكتروني</h2>
                        <p class="text-muted-soft mb-0">بعد إنشاء الطلب سيتم تحويلك إلى بوابة Tap لإتمام الدفع بأمان.</p>
                    </article>
                @else
                    <article class="surface-card p-4 mb-4">
                        <h2 class="h5 mb-3">تعليمات التحويل</h2>
                        <p class="text-muted-soft">{{ $bank['instructions'] }}</p>
                        <ul class="list-unstyled d-grid gap-2 mb-0">
                            <li><strong>البنك:</strong> {{ $bank['bank_name'] }}</li>
                            <li><strong>اسم الحساب:</strong> {{ $bank['account_name'] }}</li>
                            <li><strong>الآيبان:</strong> {{ $bank['iban'] }}</li>
                        </ul>
                    </article>
                @endif

                <form class="surface-card p-4 d-grid gap-3" method="POST" action="{{ route('subscription-plans.checkout.store', $plan) }}">
                    @csrf

                    @if ($driver !== 'demo')
                        <div>
                            <label class="form-label" for="customer_note">ملاحظة التحويل (اختياري)</label>
                            <textarea class="form-control" id="customer_note" name="customer_note" rows="3" placeholder="رقم العملية أو أي ملاحظة تساعدنا في التأكيد">{{ old('customer_note') }}</textarea>
                        </div>
                    @endif

                    <button class="btn btn-brand btn-lg" type="submit">
                        @if ($driver === 'demo')
                            تفعيل الاشتراك الآن (تجريبي)
                        @elseif (in_array($driver, ['tap', 'stripe']))
                            الانتقال للدفع الإلكتروني
                        @else
                            إنشاء طلب الاشتراك
                        @endif
                    </button>
                    <a class="btn btn-soft" href="{{ route('subscription-plans.index') }}">العودة للخطط</a>
                </form>
            </div>
        </div>
    </section>
@endsection
