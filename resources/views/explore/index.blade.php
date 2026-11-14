<x-app-layout :title="__('SocialHub — Explore & Discover')">
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

        <!-- Search Bar Header -->
        <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 shadow-sm p-4 ring-1 ring-slate-900/5">
            <form action="{{ route('explore') }}" method="GET" class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="q" value="{{ $query }}" placeholder="{{ __('Search creators by name or username, or topics...') }}"
                    class="w-full pl-11 pr-24 py-3 rounded-xl border border-slate-200/70 bg-white/70 backdrop-blur-sm text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-xs">
                <button type="submit"
                    class="absolute inset-y-1.5 right-1.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition cursor-pointer">
                    {{ __('Search') }}
                </button>
            </form>
        </div>

        <!-- Matching Creators Grid -->
        @if ($users->isNotEmpty())
            <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 shadow-sm p-5 space-y-4 ring-1 ring-slate-900/5">
                <h2 class="text-sm font-bold text-slate-900">{{ $query ? __('Matching Creators') : __('Featured Creators') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($users as $user)
                        <div class="p-3.5 rounded-xl border border-slate-200/60 bg-white/60 backdrop-blur-sm flex items-center justify-between gap-3">
                            <a href="{{ route('profile.show', $user->username) }}" class="flex items-center gap-2.5 min-w-0 group">
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                                <div class="truncate">
                                    <p class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 truncate">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">@<span>{{ $user->username }}</span></p>
                                </div>
                            </a>
                            @if (Auth::check() && Auth::id() !== $user->id)
                                <button onclick="toggleFollow({{ $user->id }}, this)"
                                    data-follow-label="{{ __('Follow') }}"
                                    data-following-label="{{ __('Following') }}"
                                    class="px-3 py-1 rounded-lg text-xs font-semibold transition cursor-pointer {{ Auth::user()->isFollowing($user) ? 'bg-white border border-slate-200 text-slate-700' : 'bg-indigo-600 text-white hover:bg-indigo-500' }}">
                                    {{ Auth::user()->isFollowing($user) ? __('Following') : __('Follow') }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Posts Section -->
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-slate-900">{{ $query ? __('Search Results for :query', ['query' => '"'.$query.'"']) : __('Trending Posts') }}</h2>

            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="text-center py-12 bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 p-8 text-slate-400 text-xs ring-1 ring-slate-900/5">
                    {{ __('No posts found matching your search. Try different keywords!') }}
                </div>
            @endforelse

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
