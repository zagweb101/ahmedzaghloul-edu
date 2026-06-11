<?php

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\User;
use App\Notifications\CommunityInteractionNotification;

class CommunityNotifier
{
    public function comment(CommunityPost $post, User $actor, ?string $preview = null): void
    {
        $this->notifyPostOwner($post, $actor, 'comment', $preview);
    }

    public function like(CommunityPost $post, User $actor): void
    {
        $this->notifyPostOwner($post, $actor, 'like');
    }

    private function notifyPostOwner(
        CommunityPost $post,
        User $actor,
        string $action,
        ?string $preview = null,
    ): void {
        if ($post->user_id === $actor->id) {
            return;
        }

        $post->user?->notify(new CommunityInteractionNotification(
            action: $action,
            post: $post,
            actor: $actor,
            preview: $preview,
        ));
    }
}
