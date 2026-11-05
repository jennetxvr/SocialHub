<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load('profile');

        return view('settings.index', [
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:320'],
            'location' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:6144'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
        ]);

        $profileData = [
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'website' => $validated['website'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->profile?->avatar_path) {
                Storage::disk('public')->delete($user->profile->avatar_path);
            }
            $avatarName = Str::uuid().'.'.$request->file('avatar')->getClientOriginalExtension();
            $profileData['avatar_path'] = $request->file('avatar')->storeAs('avatars', $avatarName, 'public');
        }

        if ($request->hasFile('banner')) {
            if ($user->profile?->banner_path) {
                Storage::disk('public')->delete($user->profile->banner_path);
            }
            $bannerName = Str::uuid().'.'.$request->file('banner')->getClientOriginalExtension();
            $profileData['banner_path'] = $request->file('banner')->storeAs('banners', $bannerName, 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        $isPrivate = $request->boolean('is_private');

        Auth::user()->profile()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['is_private' => $isPrivate]
        );

        return back()->with('success', 'Privacy settings updated!');
    }
}

