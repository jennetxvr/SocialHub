<x-app-layout title="SocialHub — Direct Messages">
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm overflow-hidden flex h-[calc(100vh-140px)] min-h-[500px] ring-1 ring-slate-900/5">

            <!-- Left Pane: Conversations List (w-full md:w-80 lg:w-96) -->
            <div class="{{ $activeConversation ? 'hidden md:flex' : 'flex' }} w-full md:w-80 lg:w-96 flex-col border-r border-slate-200">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Messages</h2>
                    <span class="text-xs text-slate-400 font-medium">{{ $conversations->count() }} conversations</span>
                </div>

                <!-- List of chats -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                    @forelse ($conversations as $conv)
                        @php
                            $partner = $conv->users->firstWhere('id', '!=', Auth::id());
                            $unread = $conv->unreadCountFor(Auth::user());
                            $isActive = $activeConversation && $activeConversation->id === $conv->id;
                        @endphp
                        <a href="{{ route('messages.show', $conv) }}"
                            class="flex items-center gap-3 p-4 hover:bg-slate-50 transition {{ $isActive ? 'bg-indigo-50/70 border-l-4 border-indigo-600' : '' }}">
                            <div class="relative shrink-0">
                                <img src="{{ $partner ? $partner->avatarUrl() : 'https://ui-avatars.com/api/?name=Chat' }}" alt="Chat"
                                    class="w-12 h-12 rounded-full object-cover border border-slate-200">
                                @if ($unread > 0)
                                    <span class="absolute top-0 right-0 w-3 h-3 bg-indigo-600 rounded-full ring-2 ring-white"></span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-xs font-bold text-slate-900 truncate">{{ $partner ? $partner->name : 'Conversation' }}</h4>
                                    @if ($conv->latestMessage)
                                        <time class="text-[10px] text-slate-400">{{ $conv->latestMessage->created_at->diffForHumans(null, true) }}</time>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 truncate {{ $unread > 0 ? 'font-semibold text-slate-900' : '' }}">
                                    @if ($conv->latestMessage)
                                        {{ $conv->latestMessage->user_id === Auth::id() ? 'You: ' : '' }}{{ $conv->latestMessage->body }}
                                    @else
                                        Start a conversation
                                    @endif
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            No conversations yet. Visit someone's profile to send them a message!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Pane: Active Chat Window -->
            <div class="{{ $activeConversation ? 'flex' : 'hidden md:flex' }} flex-1 flex-col bg-slate-50/50">
                @if ($activeConversation && $otherUser)
                    <!-- Chat Header -->
                    <div class="p-4 bg-white/70 backdrop-blur-md border-b border-slate-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Mobile Back Button -->
                            <a href="{{ route('messages.index') }}" class="md:hidden p-1 text-slate-500 hover:text-slate-800">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </a>
                            <a href="{{ route('profile.show', $otherUser->username) }}" class="flex items-center gap-2.5 group">
                                <img src="{{ $otherUser->avatarUrl() }}" alt="{{ $otherUser->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $otherUser->name }}</h3>
                                    <p class="text-[11px] text-slate-400">@<span>{{ $otherUser->username }}</span></p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Message History -->
                    <div id="messages-container" class="flex-1 p-4 overflow-y-auto space-y-3 custom-scrollbar">
                        @foreach ($messages as $msg)
                            @php $isMe = $msg->user_id === Auth::id(); @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}" data-id="{{ $msg->id }}">
                                <div class="max-w-[75%] sm:max-w-md {{ $isMe ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-2xl rounded-tr-sm shadow-sm' : 'bg-white/90 backdrop-blur-xs text-slate-800 rounded-2xl rounded-tl-sm border border-white/80 shadow-xs ring-1 ring-slate-900/5' }} px-4 py-2.5 space-y-1">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $msg->body }}</p>
                                    <div class="text-[10px] {{ $isMe ? 'text-indigo-200 text-right' : 'text-slate-400' }}">
                                        {{ $msg->created_at->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Message Input Bar -->
                    <div class="p-3.5 bg-white/70 backdrop-blur-md border-t border-slate-200/60">
                        <form id="message-form" class="flex items-center gap-2" onsubmit="handleSendMessage(event)">
                            @csrf
                            <input type="text" id="message-input" name="body" placeholder="Write a message..." required autocomplete="off"
                                class="flex-1 px-4 py-2.5 bg-slate-100 border-0 rounded-2xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <button type="submit"
                                class="p-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-sm cursor-pointer">
                                <svg class="w-5 h-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Instant Poll Script -->
                    <script>
                        const container = document.getElementById('messages-container');
                        container.scrollTop = container.scrollHeight;

                        let lastMessageId = {{ $messages->last()?->id ?? 0 }};

                        async function pollNewMessages() {
                            try {
                                const response = await fetch('{{ route('messages.poll', $activeConversation) }}?last_id=' + lastMessageId);
                                if (!response.ok) return;
                                const messages = await response.json();

                                if (messages.length > 0) {
                                    messages.forEach(msg => {
                                        if (msg.id > lastMessageId) {
                                            lastMessageId = msg.id;
                                            const bubble = document.createElement('div');
                                            bubble.className = 'flex ' + (msg.is_me ? 'justify-end' : 'justify-start');
                                            bubble.dataset.id = msg.id;
                                            bubble.innerHTML = `
                                                <div class="max-w-[75%] sm:max-w-md ${msg.is_me ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white rounded-2xl rounded-tr-sm shadow-sm' : 'bg-white/90 backdrop-blur-xs text-slate-800 rounded-2xl rounded-tl-sm border border-white/80 shadow-xs ring-1 ring-slate-900/5'} px-4 py-2.5 space-y-1">
                                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${msg.body}</p>
                                                    <div class="text-[10px] ${msg.is_me ? 'text-indigo-200 text-right' : 'text-slate-400'}">
                                                        ${msg.created_at}
                                                    </div>
                                                </div>
                                            `;
                                            container.appendChild(bubble);
                                        }
                                    });
                                    container.scrollTop = container.scrollHeight;
                                }
                            } catch (e) {}
                        }

                        setInterval(pollNewMessages, 3000);

                        async function handleSendMessage(e) {
                            e.preventDefault();
                            const input = document.getElementById('message-input');
                            const body = input.value.trim();
                            if (!body) return;

                            input.value = '';
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                            try {
                                const res = await fetch('{{ route('messages.send', $activeConversation) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ body })
                                });
                                if (res.ok) {
                                    await pollNewMessages();
                                }
                            } catch (err) {}
                        }
                    </script>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Your Messages</h3>
                        <p class="text-xs text-slate-500 max-w-xs mt-1">Select an active conversation on the left or send a direct message to any creator on SocialHub.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

