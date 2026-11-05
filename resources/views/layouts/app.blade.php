<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SocialHub' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50">
    <div class="min-h-full flex flex-col md:flex-row">

        <!-- Desktop & Tablet Left Sidebar -->
        <aside class="hidden md:flex md:w-64 lg:w-72 flex-col justify-between border-r border-slate-200/80 bg-white p-5 sticky top-0 h-screen z-20">
            <div class="space-y-6">
                <!-- Brand Logo -->
                <a href="{{ route('feed') }}" class="flex items-center gap-2.5 px-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900">Social<span class="text-indigo-600">Hub</span></span>
                </a>

                <!-- Navigation Links -->
                <nav class="space-y-1">
                    <!-- Feed -->
                    <a href="{{ route('feed') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('feed') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('feed') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>

                    <!-- Explore -->
                    <a href="{{ route('explore') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('explore') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('explore') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Explore</span>
                    </a>

                    @auth
                    <!-- Notifications -->
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('notifications.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <div class="flex items-center gap-3.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('notifications.*') ? '2.5' : '2' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>Notifications</span>
                        </div>
                        @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                        <span id="unread-badge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-600 text-white">
                            {{ $unreadCount }}
                        </span>
                    </a>

                    <!-- Direct Messages -->
                    <a href="{{ route('messages.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('messages.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('messages.*') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Messages</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile.show', Auth::user()->username) }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->is('@'.Auth::user()->username) ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->is('@'.Auth::user()->username) ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profile</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ request()->routeIs('settings.*') ? '2.5' : '2' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Sign In</span>
                    </a>
                    @endauth
                </nav>
            </div>

            <!-- User Pill / Logout -->
            @auth
            <div class="pt-4 border-t border-slate-200/80">
                <div class="flex items-center justify-between">
                    <a href="{{ route('profile.show', Auth::user()->username) }}" class="flex items-center gap-3 min-w-0">
                        <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                        <div class="truncate">
                            <p class="text-sm font-bold text-slate-900 truncate leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate">@<span>{{ Auth::user()->username }}</span></p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Mobile Top Header -->
        <header class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200/80 sticky top-0 z-30">
            <a href="{{ route('feed') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight text-slate-900">Social<span class="text-indigo-600">Hub</span></span>
            </a>

            @auth
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="p-2 text-slate-600 hover:text-indigo-600 relative">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if (Auth::user()->unreadNotifications()->count() > 0)
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-indigo-600 rounded-full ring-2 ring-white"></span>
                    @endif
                </a>
                <a href="{{ route('profile.show', Auth::user()->username) }}">
                    <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                </a>
            </div>
            @else
            <a href="{{ route('login') }}" class="text-sm font-semibold text-indigo-600">Sign In</a>
            @endauth
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 pb-20 md:pb-6">
            <!-- Flash Message Banner -->
            @if (session('success'))
                <div class="max-w-2xl mx-auto mt-4 px-4">
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="max-w-2xl mx-auto mt-4 px-4">
                    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-sm font-medium flex items-center justify-between">
                        <span>{{ session('info') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-indigo-600 hover:text-indigo-900">&times;</button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="max-w-2xl mx-auto mt-4 px-4">
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>

        <!-- Mobile Bottom Nav Bar -->
        <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200/80 px-4 py-2.5 flex items-center justify-around z-30">
            <a href="{{ route('feed') }}" class="p-2 {{ request()->routeIs('feed') ? 'text-indigo-600' : 'text-slate-500' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
            <a href="{{ route('explore') }}" class="p-2 {{ request()->routeIs('explore') ? 'text-indigo-600' : 'text-slate-500' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </a>
            @auth
            <a href="{{ route('messages.index') }}" class="p-2 {{ request()->routeIs('messages.*') ? 'text-indigo-600' : 'text-slate-500' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </a>
            <a href="{{ route('profile.show', Auth::user()->username) }}" class="p-2 {{ request()->is('@'.Auth::user()->username) ? 'text-indigo-600' : 'text-slate-500' }}">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </a>
            @endauth
        </nav>
    </div>

    <!-- Notification count poller script -->
    @auth
    <script>
        function checkNotifications() {
            fetch('{{ route('notifications.unreadCount') }}')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('unread-badge');
                    if (badge) {
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                }).catch(() => {});
        }
        setInterval(checkNotifications, 30000);
    </script>
    @endauth
</body>
</html>

