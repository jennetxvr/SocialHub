<x-app-layout title="SocialHub — Home Feed">
    <div class="max-w-6xl mx-auto px-4 py-6 flex gap-8">

        <!-- Center Feed (w-full max-w-2xl) -->
        <div class="flex-1 max-w-2xl space-y-6">

            <!-- Post Creator Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5">
                <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="flex items-start gap-3">
                        <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                        <div class="flex-1">
                            <textarea name="content" rows="3" required
                                placeholder="What is happening in your world, {{ explode(' ', Auth::user()->name)[0] }}?"
                                class="w-full text-[15px] placeholder-slate-400 border-0 focus:ring-0 p-0 resize-none focus:outline-none text-slate-800"></textarea>

                            <!-- Image Preview Container -->
                            <div id="image-preview-container" class="hidden mt-3 grid grid-cols-3 gap-2"></div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <!-- Image Upload Button -->
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Photo</span>
                                <input type="file" name="images[]" id="post-images" multiple accept="image/*" class="hidden" onchange="previewImages(event)">
                            </label>

                            <!-- Visibility Selector -->
                            <select name="visibility" class="text-xs font-medium text-slate-600 bg-slate-100 border-0 rounded-lg px-2.5 py-1.5 focus:ring-1 focus:ring-indigo-500 cursor-pointer">
                                <option value="public">Public</option>
                                <option value="followers">Followers only</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-xs tracking-wide shadow-sm shadow-indigo-600/20 transition cursor-pointer">
                            Post
                        </button>
                    </div>
                </form>
            </div>

            <!-- Feed Filter Tabs -->
            <div class="flex items-center border-b border-slate-200">
                <a href="{{ route('feed', ['tab' => 'for-you']) }}"
                    class="flex-1 text-center py-3 text-sm font-bold border-b-2 transition {{ $tab === 'for-you' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    For You
                </a>
                <a href="{{ route('feed', ['tab' => 'following']) }}"
                    class="flex-1 text-center py-3 text-sm font-bold border-b-2 transition {{ $tab === 'following' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                    Following
                </a>
            </div>

            <!-- Post Stream -->
            @forelse ($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="text-center py-12 bg-white rounded-2xl border border-slate-200/80 p-8">
                    <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No posts in this feed yet</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Follow other creators or be the first to share what is on your mind today!</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Right Rail / Suggestions Sidebar (Desktop only) -->
        <aside class="hidden lg:block w-80 space-y-6">
            <!-- Search Widget -->
            <form action="{{ route('explore') }}" method="GET" class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="q" placeholder="Search creators & topics..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
            </form>

            <!-- Who to Follow -->
            @if ($suggestedUsers->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Who to follow</h3>
                    <div class="space-y-4">
                        @foreach ($suggestedUsers as $user)
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('profile.show', $user->username) }}" class="flex items-center gap-2.5 min-w-0 group">
                                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                    <div class="truncate">
                                        <p class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition truncate">{{ $user->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">@<span>{{ $user->username }}</span></p>
                                    </div>
                                </a>
                                <button onclick="toggleFollow({{ $user->id }}, this)"
                                    class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition cursor-pointer">
                                    Follow
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Platform Footer -->
            <div class="text-[11px] text-slate-400 space-x-2 px-2">
                <span>© 2026 SocialHub</span>
                <span>•</span>
                <a href="{{ route('explore') }}" class="hover:underline">Explore</a>
                <span>•</span>
                <a href="{{ route('settings.index') }}" class="hover:underline">Settings</a>
            </div>
        </aside>
    </div>

    <!-- Image Preview Script -->
    <script>
        function previewImages(event) {
            const container = document.getElementById('image-preview-container');
            container.innerHTML = '';
            const files = event.target.files;

            if (files.length > 0) {
                container.classList.remove('hidden');
                for (let i = 0; i < files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-24 object-cover rounded-lg border border-slate-200';
                        container.appendChild(img);
                    }
                    reader.readAsDataURL(files[i]);
                }
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>

