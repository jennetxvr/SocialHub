<x-app-layout title="SocialHub — Notifications">
    <div class="max-w-2xl mx-auto px-4 py-6 space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900">Notifications</h1>
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 transition cursor-pointer">
                    Mark all as read
                </button>
            </form>
        </div>

        <!-- Notification List -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm divide-y divide-slate-100/80 overflow-hidden ring-1 ring-slate-900/5">
            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                @endphp
                <div class="p-4 flex items-start gap-3.5 transition {{ $isUnread ? 'bg-indigo-50/40' : 'hover:bg-slate-50' }}">
                    <!-- Icon Indicator -->
                    <div class="shrink-0 mt-0.5">
                        @if (($data['type'] ?? '') === 'like')
                            <div class="w-8 h-8 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </div>
                        @elseif (($data['type'] ?? '') === 'comment')
                            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                        @else
                            <div class="w-8 h-8 rounded-full bg-violet-50 text-violet-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="min-w-0 flex-1 space-y-1">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('profile.show', $data['user_username'] ?? '') }}" class="font-bold text-xs text-slate-900 hover:text-indigo-600">
                                {{ $data['user_name'] ?? 'Someone' }}
                            </a>
                            <span class="text-xs text-slate-600">{{ $data['message'] ?? 'sent an update.' }}</span>
                        </div>

                        @if (isset($data['comment_preview']))
                            <p class="text-xs text-slate-500 italic">"{{ $data['comment_preview'] }}"</p>
                        @endif

                        @if (isset($data['post_id']))
                            <a href="{{ route('posts.show', $data['post_id']) }}" class="text-[11px] font-semibold text-indigo-600 hover:underline block">
                                View post →
                            </a>
                        @endif

                        <time class="text-[10px] text-slate-400 block">{{ $notification->created_at->diffForHumans() }}</time>
                    </div>

                    @if ($isUnread)
                        <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0 mt-2"></span>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 p-8 text-slate-400 text-xs">
                    You're all caught up! No notifications yet.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>

