<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's extended profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get all posts created by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all comments authored by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all post likes submitted by the user.
     */
    public function postLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * Get all posts liked by the user.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    /**
     * Get all users following this user.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get all users this user is following.
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get all conversations the user is participating in.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot(['last_read_at', 'is_muted'])
            ->withTimestamps();
    }

    /**
     * Get all chat messages authored by the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Determine if this user is following another user.
     */
    public function isFollowing(User|int $user): bool
    {
        $targetId = $user instanceof User ? $user->id : $user;

        return $this->following()->where('following_id', $targetId)->wherePivot('status', 'accepted')->exists();
    }

    /**
     * Determine if this user is followed by another user.
     */
    public function isFollowedBy(User|int $user): bool
    {
        $targetId = $user instanceof User ? $user->id : $user;

        return $this->followers()->where('follower_id', $targetId)->wherePivot('status', 'accepted')->exists();
    }

    /**
     * Get the avatar URL or return standard initial avatar fallback.
     */
    public function avatarUrl(): string
    {
        if ($this->profile?->avatar_path) {
            return asset('storage/'.$this->profile->avatar_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7C3AED&background=EDE9FE';
    }
}
