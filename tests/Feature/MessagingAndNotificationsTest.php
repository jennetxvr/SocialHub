<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewFollowerNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can start a direct conversation and send messages', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Start conversation
    $response = $this->actingAs($userA)->post("/messages/start/{$userB->id}");
    $conversation = Conversation::first();
    $response->assertRedirect(route('messages.show', $conversation));

    // Send a message
    $this->actingAs($userA)->postJson("/messages/{$conversation->id}", [
        'body' => 'Hey there from User A!',
    ])->assertStatus(200);

    expect(Message::where('body', 'Hey there from User A!')->exists())->toBeTrue()
        ->and($conversation->unreadCountFor($userB))->toBe(1);

    // Poll messages as User B
    $pollResponse = $this->actingAs($userB)->getJson("/messages/{$conversation->id}/poll?last_id=0");
    $pollResponse->assertStatus(200);
    expect($pollResponse->json())->toHaveCount(1);
});

test('user can follow and unfollow other users', function () {
    $follower = User::factory()->create();
    $target = User::factory()->create();

    // Follow
    $this->actingAs($follower)->postJson("/users/{$target->id}/follow")
        ->assertJson(['following' => true, 'followers_count' => 1]);

    expect($follower->isFollowing($target))->toBeTrue();

    // Unfollow
    $this->actingAs($follower)->postJson("/users/{$target->id}/follow")
        ->assertJson(['following' => false, 'followers_count' => 0]);

    expect($follower->isFollowing($target))->toBeFalse();
});

test('user can view notifications and mark all as read', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $user->notify(new NewFollowerNotification($other));

    expect($user->unreadNotifications)->toHaveCount(1);

    $this->actingAs($user)->get('/notifications')->assertStatus(200);

    expect($user->fresh()->unreadNotifications)->toHaveCount(0);
});

