<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function isConfigured(): bool
    {
        return config('push.enabled')
            && config('push.vapid.public_key')
            && config('push.vapid.private_key');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, body: string, url: string}
     */
    public function formatPayload(array $data): array
    {
        $action = (string) ($data['action'] ?? '');

        $title = match ($action) {
            'subscription_activated' => 'تم تفعيل اشتراكك',
            'subscription_expiring' => 'اشتراكك على وشك الانتهاء',
            'subscription_expired' => 'انتهى اشتراكك',
            'live_event_started' => 'بدأ اللايف',
            'live_event_registration' => 'تم حجز اللايف',
            'live_event_reminder' => 'تذكير بلايف قادم',
            'like' => 'إعجاب جديد',
            'comment' => 'تعليق جديد',
            default => config('app.name'),
        };

        $body = (string) (
            $data['preview']
            ?? $data['event_title']
            ?? $data['plan_name']
            ?? $data['post_title']
            ?? 'لديك إشعار جديد داخل المنصة.'
        );

        return [
            'title' => $title,
            'body' => $body,
            'url' => (string) ($data['url'] ?? route('notifications.index')),
        ];
    }

    public function sendToUser(User $user, string $title, string $body, ?string $url = null): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $subscriptions = $user->pushSubscriptions()->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url ?? route('notifications.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('push.vapid.subject'),
                'publicKey' => config('push.vapid.public_key'),
                'privateKey' => config('push.vapid.private_key'),
            ],
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding,
                ]),
                $payload,
            );
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                continue;
            }

            $endpoint = $report->getRequest()->getUri()->__toString();

            if (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                PushSubscription::where('endpoint', $endpoint)->delete();
            }

            Log::warning('Web push delivery failed.', [
                'endpoint' => $endpoint,
                'reason' => $report->getReason(),
            ]);
        }
    }
}
