@if (config('payments.demo_mode'))
    <div class="demo-mode-banner text-center py-2 px-3">
        <strong>وضع تجريبي</strong>
        <span class="mx-2">·</span>
        الدفع: {{ \App\Support\PaymentDisplay::driverLabel() }}
        <span class="mx-2">·</span>
        لا تستخدم بيانات بنكية أو بطاقات حقيقية
    </div>
@endif
