<?php

namespace App\Notifications;

use App\Models\SubscriptionPlan;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification
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
            ->subject('تم تفعيل اشتراكك')
            ->greeting('أهلًا ' . $notifiable->name)
            ->line('تم تفعيل خطة: ' . $this->plan->name)
            ->line('يمكنك الآن الوصول إلى المحتوى المشترك داخل المنصة.')
            ->action('الذهاب إلى لوحتي', route('dashboard'));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => 'subscription_activated',
            'plan_name' => $this->plan->name,
            'plan_slug' => $this->plan->slug,
            'url' => route('dashboard'),
        ];
    }
}
