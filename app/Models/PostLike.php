<?php

namespace App\Models;

use Database\Factories\PostLikeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['post_id', 'user_id'])]
class PostLike extends Model
{
    /** @use HasFactory<PostLikeFactory> */
    use HasFactory;

    /**
     * Get the post that was liked.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who liked the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
