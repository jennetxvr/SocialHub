<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollowerNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $follower
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'follow',
            'user_id' => $this->follower->id,
            'user_name' => $this->follower->name,
            'user_username' => $this->follower->username,
            'user_avatar' => $this->follower->avatarUrl(),
            'message' => 'started following you.',
        ];
    }
}

