<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'visibility' => ['nullable', 'in:public,followers,private'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'visibility' => $validated['visibility'] ?? 'public',
            'reply_settings' => 'everyone',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('posts', $filename, 'public');

                Media::create([
                    'mediable_type' => Post::class,
                    'mediable_id' => $post->id,
                    'file_path' => $path,
                    'disk' => 'public',
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return back()->with('success', 'Your post was published!');
    }

    public function show(Post $post): View
    {
        $post->load(['user.profile', 'media', 'likes']);
        $post->loadCount(['comments', 'likes']);

        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user.profile', 'replies.user.profile'])
            ->latest()
            ->get();

        return view('feed.show', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete associated media files
        foreach ($post->media as $media) {
            Storage::disk($media->disk)->delete($media->file_path);
            $media->delete();
        }

        $post->delete();

        return redirect()->route('feed')->with('success', 'Post deleted.');
    }
}

