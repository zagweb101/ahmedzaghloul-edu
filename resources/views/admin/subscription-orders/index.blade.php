@extends('layouts.app')

@section('title', 'طلبات الاشتراك')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div>
                    <p class="fw-bold text-accent mb-2">المدفوعات</p>
                    <h1 class="display-6 fw-bold mb-0">طلبات الاشتراك</h1>
                </div>
            </div>

            @include('admin.partials.nav')

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-grid gap-3">
                @forelse ($orders as $order)
                    <article class="surface-card p-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <span class="badge {{ $order->status === 'pending' ? 'text-bg-warning' : ($order->status === 'paid' ? 'text-bg-success' : 'text-bg-secondary') }} mb-2">
                                    {{ $order->status }}
                                </span>
                                <h2 class="h5">{{ $order->reference }}</h2>
                                <p class="text-muted-soft mb-1">{{ $order->user?->name }} · {{ $order->user?->email }}</p>
                                <p class="text-muted-soft mb-0">{{ $order->plan?->name }} · {{ $order->formattedAmount() }}</p>
                                @if ($order->customer_note)
                                    <small class="text-muted-soft d-block mt-2">ملاحظة: {{ $order->customer_note }}</small>
                                @endif
                            </div>

                            @if ($order->isPending())
                                <div class="d-flex flex-wrap gap-2 align-self-start">
                                    <form method="POST" action="{{ route('admin.subscription-orders.approve', $order) }}">
                                        @csrf
                                        <button class="btn btn-brand" type="submit">تأكيد الدفع</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.subscription-orders.cancel', $order) }}">
                                        @csrf
                                        <button class="btn btn-outline-danger" type="submit">إلغاء</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="surface-card p-4 text-muted-soft">لا توجد طلبات اشتراك حتى الآن.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>
@endsection
