<?php

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Follow;
use App\Models\Media;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user has one profile and profile belongs to user', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    expect($user->profile)->not->toBeNull()
        ->and($user->profile->id)->toBe($profile->id)
        ->and($profile->user->id)->toBe($user->id);
});

test('user has many posts and post belongs to user', function () {
    $user = User::factory()->create();
    $posts = Post::factory(3)->create(['user_id' => $user->id]);

    expect($user->posts)->toHaveCount(3)
        ->and($posts->first()->user->id)->toBe($user->id);
});

test('post can be liked by users and helper detects likes correctly', function () {
    $author = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    PostLike::factory()->create([
        'post_id' => $post->id,
        'user_id' => $liker->id,
    ]);

    expect($post->likes)->toHaveCount(1)
        ->and($post->isLikedBy($liker))->toBeTrue()
        ->and($post->isLikedBy($author))->toBeFalse()
        ->and($liker->likedPosts->pluck('id'))->toContain($post->id);
});

test('comments support nested replies', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $parentComment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $author->id,
    ]);

    $reply = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => $author->id,
        'parent_id' => $parentComment->id,
    ]);

    expect($parentComment->replies)->toHaveCount(1)
        ->and($parentComment->replies->first()->id)->toBe($reply->id)
        ->and($reply->parent->id)->toBe($parentComment->id);
});

test('user follow system tracks followers and following correctly', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    Follow::create([
        'follower_id' => $userA->id,
        'following_id' => $userB->id,
        'status' => 'accepted',
    ]);

    expect($userA->isFollowing($userB))->toBeTrue()
        ->and($userB->isFollowedBy($userA))->toBeTrue()
        ->and($userB->isFollowing($userA))->toBeFalse();
});

test('conversations manage participants, messages, and unread counts', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $conversation = Conversation::create(['type' => 'direct']);
    $conversation->users()->attach([
        $userA->id => ['last_read_at' => now()->subHour()],
        $userB->id => ['last_read_at' => now()],
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $userB->id,
        'body' => 'Hello Sophia!',
    ]);

    expect($conversation->users)->toHaveCount(2)
        ->and($conversation->messages)->toHaveCount(1)
        ->and($conversation->latestMessage->body)->toBe('Hello Sophia!')
        ->and($conversation->unreadCountFor($userA))->toBe(1)
        ->and($conversation->unreadCountFor($userB))->toBe(0);

    $conversation->markAsReadFor($userA);
    expect($conversation->unreadCountFor($userA))->toBe(0);
});

test('media attaches polymorphically to posts', function () {
    $post = Post::factory()->create();

    $media = Media::factory()->create([
        'mediable_type' => Post::class,
        'mediable_id' => $post->id,
    ]);

    expect($post->media)->toHaveCount(1)
        ->and($post->media->first()->id)->toBe($media->id)
        ->and($media->mediable->id)->toBe($post->id);
});
