<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim($request->query('q', ''));

        $users = collect();
        $posts = collect();

        if ($query !== '') {
            $users = User::with('profile')
                ->where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->take(10)
                ->get();

            $posts = Post::with(['user.profile', 'media', 'likes'])
                ->withCount(['comments', 'likes'])
                ->where('content', 'like', "%{$query}%")
                ->where('visibility', 'public')
                ->latest()
                ->paginate(12)
                ->withQueryString();
        } else {
            // Trending posts (sorted by likes + comments)
            $posts = Post::with(['user.profile', 'media', 'likes'])
                ->withCount(['comments', 'likes'])
                ->where('visibility', 'public')
                ->orderByRaw('(likes_count + comments_count) DESC')
                ->latest()
                ->paginate(12);

            $users = User::with('profile')
                ->inRandomOrder()
                ->take(6)
                ->get();
        }

        return view('explore.index', [
            'query' => $query,
            'users' => $users,
            'posts' => $posts,
        ]);
    }
}
