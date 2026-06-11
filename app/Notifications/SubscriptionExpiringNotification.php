<?php

namespace App\Notifications;

use App\Models\SubscriptionPlan;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;
    use UsesPlatformChannels;

    public function __construct(public SubscriptionPlan $plan) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $this->platformChannels();
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('اشتراكك على وشك الانتهاء')
            ->greeting('أهلًا ' . $notifiable->name)
            ->line('خطة ' . $this->plan->name . ' ستنتهي قريبًا.')
            ->line('جدّد اشتراكك للاستمرار في الوصول إلى المحتوى والمجتمع واللايفات.')
            ->action('تجديد الاشتراك', route('subscription-plans.show', $this->plan));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => 'subscription_expiring',
            'plan_name' => $this->plan->name,
            'plan_slug' => $this->plan->slug,
            'url' => route('subscription-plans.show', $this->plan),
        ];
    }
}
