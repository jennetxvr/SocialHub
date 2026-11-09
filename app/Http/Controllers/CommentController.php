<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Post $post, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:500'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
        ]);

        $post->increment('comments_count');

        // Notify post author if not self
        if ($post->user_id !== Auth::id()) {
            $post->user->notify(new NewCommentNotification(Auth::user(), $post, $comment));
        }

        return back()->with('success', 'Comment posted!');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        if ($comment->user_id !== Auth::id() && $comment->post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $post = $comment->post;
        $comment->delete();
        $post->decrement('comments_count');

        return back()->with('success', 'Comment deleted.');
    }
}
