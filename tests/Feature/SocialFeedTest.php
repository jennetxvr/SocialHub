<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view home feed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

test('user can create a post', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/posts', [
        'content' => 'Hello SocialHub world! This is my first post.',
        'visibility' => 'public',
    ]);

    $response->assertSessionHas('success');
    expect(Post::where('content', 'Hello SocialHub world! This is my first post.')->exists())->toBeTrue();
});

test('post author can delete their post but others cannot', function () {
    $author = User::factory()->create();
    $stranger = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    // Stranger tries to delete
    $this->actingAs($stranger)->delete("/posts/{$post->id}")->assertStatus(403);

    // Author deletes
    $this->actingAs($author)->delete("/posts/{$post->id}")->assertRedirect(route('feed'));
    expect(Post::find($post->id))->toBeNull();
});

test('user can toggle like on a post', function () {
    $author = User::factory()->create();
    $liker = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    // Like
    $response = $this->actingAs($liker)->postJson("/posts/{$post->id}/like");
    $response->assertJson(['liked' => true, 'likes_count' => 1]);

    // Unlike
    $responseUnlike = $this->actingAs($liker)->postJson("/posts/{$post->id}/like");
    $responseUnlike->assertJson(['liked' => false, 'likes_count' => 0]);
});

test('user can post comments and nested replies', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    // Top-level comment
    $this->actingAs($user)->post("/posts/{$post->id}/comments", [
        'content' => 'This is an insightful post!',
    ]);

    $comment = Comment::where('content', 'This is an insightful post!')->first();
    expect($comment)->not->toBeNull()
        ->and($post->fresh()->comments_count)->toBe(1);

    // Nested reply
    $this->actingAs($user)->post("/posts/{$post->id}/comments", [
        'parent_id' => $comment->id,
        'content' => 'Replying to the insightful thought.',
    ]);

    expect($comment->fresh()->replies)->toHaveCount(1)
        ->and($post->fresh()->comments_count)->toBe(2);
});

