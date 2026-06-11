<?php

namespace App\Notifications;

use App\Models\LiveEvent;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiveEventStartedNotification extends Notification
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
            ->subject('بدأ اللايف: ' . $this->event->title)
            ->greeting('أهلًا ' . $notifiable->name)
            ->line('اللايف الذي حجزته بدأ الآن.')
            ->action('الانضمام للبث', route('live-events.show', $this->event));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => 'live_event_started',
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'url' => route('live-events.show', $this->event),
        ];
    }
}
