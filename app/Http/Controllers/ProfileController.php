<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(string $username, Request $request): View
    {
        $user = User::where('username', strtolower($username))
            ->with('profile')
            ->withCount(['posts', 'followers', 'following'])
            ->firstOrFail();

        $tab = $request->query('tab', 'posts');

        $postsQuery = Post::where('user_id', $user->id)
            ->with(['user.profile', 'media', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest();

        if ($tab === 'media') {
            $postsQuery->has('media');
        } elseif ($tab === 'likes') {
            $likedPostIds = $user->likedPosts()->pluck('posts.id');
            $postsQuery = Post::whereIn('id', $likedPostIds)
                ->with(['user.profile', 'media', 'likes'])
                ->withCount(['comments', 'likes'])
                ->latest();
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        $isFollowing = false;
        if (Auth::check() && Auth::id() !== $user->id) {
            $isFollowing = Auth::user()->isFollowing($user);
        }

        return view('profile.show', [
            'user' => $user,
            'tab' => $tab,
            'posts' => $posts,
            'isFollowing' => $isFollowing,
        ]);
    }
}

