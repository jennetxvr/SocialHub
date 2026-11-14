@props(['post'])

<article class="glass-post rounded-2xl p-5 hover:border-indigo-200/90 hover:shadow-xl transition duration-150">
    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('profile.show', $post->user->username) }}" class="flex items-center gap-3 group min-w-0">
            <img src="{{ $post->user->avatarUrl() }}" alt="{{ $post->user->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition truncate">{{ $post->user->name }}</span>
                    <span class="text-xs text-slate-500 truncate">@<span>{{ $post->user->username }}</span></span>
                    <span class="text-slate-300 text-xs">•</span>
                    <time class="text-xs text-slate-400 whitespace-nowrap">{{ $post->created_at->diffForHumans(null, true) }}</time>
                </div>
                @if ($post->visibility !== 'public')
                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-400">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('Followers only') }}
                    </span>
                @endif
            </div>
        </a>

        @if (Auth::id() === $post->user_id)
            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this post?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" title="{{ __('Delete post') }}" class="text-slate-400 hover:text-rose-600 p-1 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </form>
        @endif
    </div>

    <!-- Body Content -->
    <div class="mt-3 text-slate-800 text-[15px] leading-relaxed whitespace-pre-line break-words">
        {{ $post->content }}
    </div>

    <!-- Media Grid -->
    @if ($post->media->isNotEmpty())
        <div class="mt-3 rounded-xl overflow-hidden border border-slate-200/80 bg-slate-100 {{ $post->media->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }}">
            @foreach ($post->media as $media)
                <div class="relative overflow-hidden group max-h-96">
                    <img src="{{ $media->url() }}" alt="{{ __('Attachment') }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-102">
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer Actions -->
    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-slate-500">
        <!-- Likes -->
        <button onclick="toggleLike({{ $post->id }}, this)"
            class="flex items-center gap-1.5 text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition group cursor-pointer {{ $post->isLikedBy(Auth::user()) ? 'text-pink-600' : '' }}">
            <svg class="w-4 h-4 transition transform group-hover:scale-115 {{ $post->isLikedBy(Auth::user()) ? 'fill-current text-pink-600' : 'fill-none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span class="like-count font-bold">{{ $post->likes_count ?? $post->likes()->count() }}</span>
        </button>

        <!-- Comments -->
        <a href="{{ route('posts.show', $post) }}" class="flex items-center gap-1.5 text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <span>{{ $post->comments_count ?? $post->comments()->count() }}</span>
        </a>

        <!-- Share / Copy link -->
        <button onclick="navigator.clipboard.writeText('{{ route('posts.show', $post) }}'); alert('{{ __('Post link copied to clipboard!') }}');" title="{{ __('Share') }}" class="flex items-center gap-1.5 text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-slate-100 hover:text-slate-800 transition cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
            <span>{{ __('Share') }}</span>
        </button>
    </div>
</article>
