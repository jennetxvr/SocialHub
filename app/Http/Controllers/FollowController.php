<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use App\Notifications\NewFollowerNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user, Request $request): JsonResponse|RedirectResponse
    {
        $currentUserId = Auth::id();

        if ($currentUserId === $user->id) {
            abort(400, __('You cannot follow yourself.'));
        }

        $existing = Follow::where('follower_id', $currentUserId)
            ->where('following_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            Follow::create([
                'follower_id' => $currentUserId,
                'following_id' => $user->id,
                'status' => 'accepted',
            ]);
            $following = true;

            $user->notify(new NewFollowerNotification(Auth::user()));
        }

        $followersCount = $user->followers()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'following' => $following,
                'followers_count' => $followersCount,
            ]);
        }

        return back()->with('info', $following
            ? __('You are now following @:username', ['username' => $user->username])
            : __('You unfollowed @:username', ['username' => $user->username]));
    }
}
