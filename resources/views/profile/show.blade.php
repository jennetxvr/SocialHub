<x-app-layout :title="__('Profile by :name (@:username) — SocialHub', ['name' => $user->name, 'username' => $user->username])">
    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

        <!-- Profile Card -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm overflow-hidden ring-1 ring-slate-900/5">
            <!-- Banner -->
            <div class="h-44 sm:h-56 bg-gradient-to-r from-indigo-500 via-purple-500 to-violet-600 relative">
                @if ($user->profile?->banner_path)
                    <img src="{{ asset('storage/' . $user->profile->banner_path) }}" alt="{{ __('Header Banner') }}" class="w-full h-full object-cover">
                @endif
            </div>

            <!-- Profile Details Bar -->
            <div class="px-6 pb-6 pt-0 relative">
                <!-- Avatar & Actions Row -->
                <div class="flex items-end justify-between -mt-16 sm:-mt-20 mb-4 flex-wrap gap-4">
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}"
                        class="w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover border-4 border-white shadow-md bg-white">

                    <div class="flex items-center gap-2">
                        @if (Auth::id() === $user->id)
                            <a href="{{ route('settings.index') }}"
                                class="px-4 py-2 rounded-xl border border-slate-300 hover:border-slate-400 text-slate-700 font-semibold text-xs tracking-wide transition shadow-sm">
                                {{ __('Edit Profile') }}
                            </a>
                        @else
                            <!-- Follow / Unfollow Button -->
                            <button onclick="toggleFollow({{ $user->id }}, this)"
                                data-follow-label="{{ __('Follow') }}"
                                data-following-label="{{ __('Following') }}"
                                class="px-4 py-2 rounded-xl font-semibold text-xs tracking-wide transition shadow-sm cursor-pointer {{ $isFollowing ? 'bg-slate-100 text-slate-800 hover:bg-slate-200' : 'bg-indigo-600 hover:bg-indigo-500 text-white' }}">
                                {{ $isFollowing ? __('Following') : __('Follow') }}
                            </button>

                            <!-- Direct Message Button -->
                            <form method="POST" action="{{ route('messages.start', $user) }}">
                                @csrf
                                <button type="submit"
                                    class="p-2 rounded-xl border border-slate-300 hover:border-slate-400 text-slate-700 hover:text-indigo-600 transition shadow-sm cursor-pointer"
                                    title="{{ __('Send Message') }}">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Identity & Bio -->
                <div class="space-y-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-500 font-medium">@<span>{{ $user->username }}</span></p>
                    </div>

                    @if ($user->profile?->bio)
                        <p class="text-sm text-slate-700 leading-relaxed max-w-2xl whitespace-pre-line">{{ $user->profile->bio }}</p>
                    @endif

                    <!-- Location, Website, Joined -->
                    <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap">
                        @if ($user->profile?->location)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $user->profile->location }}
                            </span>
                        @endif

                        @if ($user->profile?->website)
                            <a href="{{ $user->profile->website }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-indigo-600 hover:underline">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                {{ parse_url($user->profile->website, PHP_URL_HOST) ?? $user->profile->website }}
                            </a>
                        @endif

                        <span class="inline-flex items-center gap-1 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            {{ __('Joined') }} {{ $user->created_at->translatedFormat('F Y') }}
                        </span>
                    </div>

                    <!-- Metrics -->
                    <div class="flex items-center gap-5 pt-2 text-sm">
                        <span class="text-slate-900 font-bold">{{ $user->following_count ?? $user->following()->count() }} <span class="text-slate-500 font-normal">{{ __('Following') }}</span></span>
                        <span class="text-slate-900 font-bold">{{ $user->followers_count ?? $user->followers()->count() }} <span class="text-slate-500 font-normal">{{ __('Followers') }}</span></span>
                        <span class="text-slate-900 font-bold">{{ $user->posts_count ?? $user->posts()->count() }} <span class="text-slate-500 font-normal">{{ __('Posts') }}</span></span>
                    </div>
                </div>
            </div>

            <!-- Profile Tabs -->
            <div class="flex items-center border-t border-slate-200 px-6">
                <a href="{{ route('profile.show', ['username' => $user->username, 'tab' => 'posts']) }}"
                    class="py-3 px-4 text-xs font-bold border-b-2 transition {{ $tab === 'posts' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    {{ __('Posts') }}
                </a>
                <a href="{{ route('profile.show', ['username' => $user->username, 'tab' => 'media']) }}"
                    class="py-3 px-4 text-xs font-bold border-b-2 transition {{ $tab === 'media' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    {{ __('Media') }}
                </a>
                <a href="{{ route('profile.show', ['username' => $user->username, 'tab' => 'likes']) }}"
                    class="py-3 px-4 text-xs font-bold border-b-2 transition {{ $tab === 'likes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    {{ __('Likes') }}
                </a>
            </div>
        </div>

        <!-- Tab Content Stream -->
        <div class="space-y-6">
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="text-center py-12 bg-white/80 backdrop-blur-md rounded-2xl border border-white/80 p-8 text-slate-400 text-xs ring-1 ring-slate-900/5">
                    {{ __('No posts to display in this section yet.') }}
                </div>
            @endforelse

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
