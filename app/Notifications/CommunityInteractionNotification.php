<?php

namespace App\Notifications;

use App\Models\CommunityPost;
use App\Models\User;
use App\Notifications\Concerns\UsesPlatformChannels;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityInteractionNotification extends Notification
{
    use Queueable;
    use UsesPlatformChannels;

    public function __construct(
        public string $action,
        public CommunityPost $post,
        public User $actor,
        public ?string $preview = null,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return $this->platformChannels();
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->mailSubject())
            ->greeting('أهلًا ' . $notifiable->name)
            ->line($this->actor->name . ' ' . $this->actionLabel() . '.')
            ->line('البوست: ' . $this->post->title);

        if ($this->preview) {
            $message->line('"' . $this->preview . '"');
        }

        return $message->action('عرض البوست', route('community.index') . '#post-' . $this->post->id);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'action' => $this->action,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'preview' => $this->preview,
            'url' => route('community.index') . '#post-' . $this->post->id,
        ];
    }

    private function mailSubject(): string
    {
        return match ($this->action) {
            'comment' => 'تعليق جديد على بوستك',
            'like' => 'إعجاب جديد على بوستك',
            default => 'تفاعل جديد في المجتمع',
        };
    }

    private function actionLabel(): string
    {
        return match ($this->action) {
            'comment' => 'علّق على بوستك',
            'like' => 'أعجب ببوستك',
            default => 'تفاعل مع بوستك',
        };
    }
}
