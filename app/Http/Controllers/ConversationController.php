<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $conversations = $currentUser->conversations()
            ->with(['users.profile', 'latestMessage.user'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
        ]);
    }

    public function show(Conversation $conversation): View
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        // Ensure user belongs to conversation
        if (! $conversation->users->contains('id', $currentUser->id)) {
            abort(403, 'Unauthorized conversation access.');
        }

        $conversation->markAsReadFor($currentUser);

        $conversations = $currentUser->conversations()
            ->with(['users.profile', 'latestMessage.user'])
            ->orderByDesc('last_message_at')
            ->get();

        $messages = $conversation->messages()
            ->with('user.profile')
            ->oldest()
            ->get();

        $otherUser = $conversation->users->firstWhere('id', '!=', $currentUser->id);

        return view('messages.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'otherUser' => $otherUser,
            'messages' => $messages,
        ]);
    }

    public function start(User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return redirect()->route('messages.index')->with('warning', 'You cannot message yourself.');
        }

        // Find existing 1-on-1 conversation
        $conversation = Conversation::where('type', 'direct')
            ->whereHas('users', fn ($q) => $q->where('users.id', $currentUser->id))
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'type' => 'direct',
                'last_message_at' => now(),
            ]);

            $conversation->users()->attach([
                $currentUser->id => ['last_read_at' => now()],
                $user->id => ['last_read_at' => null],
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function sendMessage(Conversation $conversation, Request $request): JsonResponse|RedirectResponse
    {
        $currentUser = Auth::user();

        if (! $conversation->users->contains('id', $currentUser->id)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $currentUser->id,
            'body' => $validated['body'],
            'type' => 'text',
        ]);

        $conversation->update(['last_message_at' => now()]);
        $conversation->markAsReadFor($currentUser);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => e($message->body),
                'user_id' => $message->user_id,
                'created_at' => $message->created_at->format('g:i A'),
            ]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    public function poll(Conversation $conversation, Request $request): JsonResponse
    {
        $currentUser = Auth::user();

        if (! $conversation->users->contains('id', $currentUser->id)) {
            return response()->json([], 403);
        }

        $lastId = $request->query('last_id', 0);

        $newMessages = $conversation->messages()
            ->with('user.profile')
            ->where('id', '>', $lastId)
            ->oldest()
            ->get();

        if ($newMessages->isNotEmpty()) {
            $conversation->markAsReadFor($currentUser);
        }

        return response()->json($newMessages->map(fn ($msg) => [
            'id' => $msg->id,
            'body' => e($msg->body),
            'user_id' => $msg->user_id,
            'is_me' => $msg->user_id === $currentUser->id,
            'sender_name' => $msg->user->name,
            'sender_avatar' => $msg->user->avatarUrl(),
            'created_at' => $msg->created_at->format('g:i A'),
        ]));
    }
}
