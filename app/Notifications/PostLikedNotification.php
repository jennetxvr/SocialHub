<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLikedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $liker,
        public Post $post
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'like',
            'user_id' => $this->liker->id,
            'user_name' => $this->liker->name,
            'user_username' => $this->liker->username,
            'user_avatar' => $this->liker->avatarUrl(),
            'post_id' => $this->post->id,
            'message' => 'liked your post.',
        ];
    }
}
