<?php

namespace App\Http\Controllers;

use App\Models\LiveEvent;
use App\Notifications\LiveEventRegistrationNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveEventController extends Controller
{
    public function index(Request $request): View
    {
        $eventsQuery = LiveEvent::query()
            ->withCount(['registrations' => fn ($query) => $query->where('status', 'registered')])
            ->where('is_published', true)
            ->orderByRaw('starts_at is null')
            ->orderBy('starts_at');

        if ($request->user()) {
            $eventsQuery->withExists([
                'registrations as registered_by_current_user' => fn ($query) => $query
                    ->where('user_id', $request->user()->id)
                    ->where('status', 'registered'),
            ]);
        }

        return view('live-events.index', [
            'events' => $eventsQuery->get(),
        ]);
    }

    public function register(Request $request, LiveEvent $liveEvent): RedirectResponse
    {
        abort_unless($liveEvent->is_published, 404);
        abort_unless($request->user()->canAccess($liveEvent->access_level), 403);

        $result = DB::transaction(function () use ($request, $liveEvent): string {
            $event = LiveEvent::query()->lockForUpdate()->findOrFail($liveEvent->id);
            $existingRegistration = $event->registrations()
                ->where('user_id', $request->user()->id)
                ->first();

            if ($existingRegistration?->status === 'registered') {
                return 'already_registered';
            }

            if (! $event->hasAvailableSeats()) {
                return 'full';
            }

            $event->registrations()->updateOrCreate(
                ['user_id' => $request->user()->id],
                [
                    'status' => 'registered',
                    'reminder_sent_at' => null,
                ],
            );

            return 'registered';
        });

        if ($result === 'registered') {
            $request->user()->notify(new LiveEventRegistrationNotification($liveEvent));
        }

        return back()->with(
            $result !== 'full' ? 'status' : 'error',
            match ($result) {
                'registered', 'already_registered' => 'تم حجز مقعدك في الفعالية.',
                default => 'عذرًا، اكتمل عدد المقاعد.',
            },
        );
    }

    public function cancel(Request $request, LiveEvent $liveEvent): RedirectResponse
    {
        $liveEvent->registrations()
            ->where('user_id', $request->user()->id)
            ->update([
                'status' => 'cancelled',
                'reminder_sent_at' => null,
            ]);

        return back()->with('status', 'تم إلغاء الحجز.');
    }
}
