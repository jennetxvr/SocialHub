<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $commenter,
        public Post $post,
        public Comment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment',
            'user_id' => $this->commenter->id,
            'user_name' => $this->commenter->name,
            'user_username' => $this->commenter->username,
            'user_avatar' => $this->commenter->avatarUrl(),
            'post_id' => $this->post->id,
            'comment_id' => $this->comment->id,
            'comment_preview' => mb_strimwidth($this->comment->content, 0, 50, '...'),
            'message' => 'commented on your post.',
        ];
    }
}
