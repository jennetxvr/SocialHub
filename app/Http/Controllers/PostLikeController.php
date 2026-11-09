<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Notifications\PostLikedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostLikeController extends Controller
{
    public function toggle(Post $post, Request $request): JsonResponse|RedirectResponse
    {
        $userId = Auth::id();
        $existingLike = PostLike::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $post->increment('likes_count');
            $liked = true;

            // Send notification if not liking own post
            if ($post->user_id !== $userId) {
                $post->user->notify(new PostLikedNotification(Auth::user(), $post));
            }
        }

        $likesCount = $post->fresh()->likes_count;

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);
        }

        return back();
    }
}
