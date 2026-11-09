<x-app-layout :title="'Post by @' . $post->user->username . ' — SocialHub'">
    <div class="max-w-2xl mx-auto px-4 py-6 space-y-6">

        <!-- Back Button -->
        <a href="{{ url()->previous() ?: route('feed') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>Back</span>
        </a>

        <!-- Main Post -->
        <x-post-card :post="$post" />

        <!-- Add Comment Form -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 shadow-sm p-4 sm:p-5 ring-1 ring-slate-900/5">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Leave a reply</h3>
            <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-3">
                @csrf
                <div class="flex items-start gap-3">
                    <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0">
                    <textarea name="content" rows="2" required placeholder="Write your thoughts..."
                        class="w-full text-sm placeholder-slate-400 border border-slate-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 resize-none"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs tracking-wide transition cursor-pointer">
                        Reply
                    </button>
                </div>
            </form>
        </div>

        <!-- Comments Discussion Thread -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900">Replies ({{ $post->comments_count ?? $post->comments()->count() }})</h3>

            @forelse ($comments as $comment)
                <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 shadow-sm p-4 space-y-3 ring-1 ring-slate-900/5">
                    <!-- Parent Comment Author & Content -->
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('profile.show', $comment->user->username) }}" class="flex items-center gap-2.5 group min-w-0">
                            <img src="{{ $comment->user->avatarUrl() }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                            <div class="truncate">
                                <span class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $comment->user->name }}</span>
                                <span class="text-[11px] text-slate-500">@<span>{{ $comment->user->username }}</span></span>
                            </div>
                        </a>
                        <div class="flex items-center gap-2">
                            <time class="text-[11px] text-slate-400">{{ $comment->created_at->diffForHumans(null, true) }}</time>
                            @if (Auth::id() === $comment->user_id || Auth::id() === $post->user_id)
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete comment" class="text-slate-300 hover:text-rose-600 transition p-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm text-slate-800 whitespace-pre-line pl-10">{{ $comment->content }}</p>

                    <!-- Nested Replies -->
                    @if ($comment->replies->isNotEmpty())
                        <div class="pl-10 space-y-3 pt-2 border-t border-slate-100">
                            @foreach ($comment->replies as $reply)
                                <div class="bg-slate-50 rounded-xl p-3 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('profile.show', $reply->user->username) }}" class="flex items-center gap-2 group">
                                            <img src="{{ $reply->user->avatarUrl() }}" alt="{{ $reply->user->name }}" class="w-6 h-6 rounded-full object-cover">
                                            <span class="text-xs font-bold text-slate-900 group-hover:text-indigo-600">{{ $reply->user->name }}</span>
                                            <span class="text-[11px] text-slate-400">@<span>{{ $reply->user->username }}</span></span>
                                        </a>
                                        <time class="text-[10px] text-slate-400">{{ $reply->created_at->diffForHumans(null, true) }}</time>
                                    </div>
                                    <p class="text-xs text-slate-800 pl-8">{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Reply Trigger Form Toggle -->
                    <details class="pl-10 pt-1 group">
                        <summary class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 cursor-pointer list-none inline-flex items-center gap-1">
                            <span>Reply to @<span>{{ $comment->user->username }}</span></span>
                        </summary>
                        <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-2 flex gap-2">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="text" name="content" required placeholder="Write a reply..."
                                class="flex-1 text-xs border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition cursor-pointer">
                                Send
                            </button>
                        </form>
                    </details>
                </div>
            @empty
                <div class="text-center py-8 bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 p-6 text-slate-400 text-xs ring-1 ring-slate-900/5">
                    Be the first person to share a comment!
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>

