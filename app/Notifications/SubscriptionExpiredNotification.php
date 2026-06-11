<?php

namespace App\Notifications;

use App\Models\SubscriptionPlan;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification
{
    use Queueable;
    use UsesPlatformChannels;

    public function __construct(public SubscriptionPlan $plan) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $this->platformChannels($notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('انتهى اشتراكك')
            ->greeting('أهلًا ' . $notifiable->name)
            ->line('انتهت صلاحية خطة ' . $this->plan->name . '.')
            ->line('يمكنك تجديد الاشتراك في أي وقت لاستعادة الوصول الكامل.')
            ->action('تجديد الاشتراك', route('subscription-plans.show', $this->plan));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => 'subscription_expired',
            'plan_name' => $this->plan->name,
            'plan_slug' => $this->plan->slug,
            'url' => route('subscription-plans.show', $this->plan),
        ];
    }
}
