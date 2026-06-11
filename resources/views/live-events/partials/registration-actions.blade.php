@php
    $isFull = $event->capacity !== null && $event->registrations_count >= $event->capacity;
    $canAccess = auth()->check() && auth()->user()->canAccess($event->access_level);
@endphp

@auth
    @if ($event->registered_by_current_user ?? false)
        <form method="POST" action="{{ route('live-events.cancel', $event) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-soft w-100" type="submit">إلغاء الحجز</button>
        </form>
    @elseif (! $canAccess)
        <a class="btn btn-brand w-100" href="{{ route('subscription-plans.index') }}">اشترك للحجز</a>
    @else
        <form method="POST" action="{{ route('live-events.register', $event) }}">
            @csrf
            <button class="btn btn-brand w-100" type="submit" @disabled($isFull)>حجز مقعد</button>
        </form>
    @endif
@else
    <a class="btn btn-brand w-100" href="{{ route('login') }}">سجل الدخول للحجز</a>
@endauth
