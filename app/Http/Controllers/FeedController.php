<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'for-you');
        $currentUser = Auth::user();

        $query = Post::with(['user.profile', 'media', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest();

        if ($tab === 'following' && $currentUser) {
            $followingIds = $currentUser->following()->pluck('users.id');
            $query->whereIn('user_id', $followingIds->push($currentUser->id));
        } else {
            $query->where(function ($q) use ($currentUser) {
                $q->where('visibility', 'public');
                if ($currentUser) {
                    $followingIds = $currentUser->following()->pluck('users.id');
                    $q->orWhereIn('user_id', $followingIds)
                        ->orWhere('user_id', $currentUser->id);
                }
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        // Suggested users to follow
        $suggestedUsers = collect();
        if ($currentUser) {
            $alreadyFollowing = $currentUser->following()->pluck('users.id')->push($currentUser->id);
            $suggestedUsers = User::with('profile')
                ->whereNotIn('id', $alreadyFollowing)
                ->inRandomOrder()
                ->take(5)
                ->get();
        }

        return view('feed.index', [
            'posts' => $posts,
            'tab' => $tab,
            'suggestedUsers' => $suggestedUsers,
        ]);
    }
}
