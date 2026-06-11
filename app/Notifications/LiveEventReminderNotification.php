<?php

namespace App\Notifications;

use App\Models\LiveEvent;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveEventReminderNotification extends Notification
{
    use Queueable;
    use UsesPlatformChannels;

    public function __construct(public LiveEvent $event) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $this->platformChannels($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تذكير: لايف قادم قريبًا')
            ->greeting('أهلًا ' . $notifiable->name)
            ->line('لايف قادم سجلت فيه: ' . $this->event->title)
            ->line('الموعد: ' . ($this->event->starts_at?->translatedFormat('d F Y - h:i A') ?? 'يحدد لاحقًا'))
            ->action('عرض اللايفات', route('live-events.index'));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => 'live_reminder',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'starts_at' => $this->event->starts_at?->toIso8601String(),
            'url' => route('live-events.index'),
        ];
    }
}
