<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user profile page can be viewed by username', function () {
    $user = User::factory()->create(['username' => 'elena']);
    Profile::factory()->create(['user_id' => $user->id, 'bio' => 'Elena bio test']);

    $response = $this->get('/@elena');

    $response->assertStatus(200)
        ->assertSee('elena')
        ->assertSee('Elena bio test');
});

test('user can update their profile information and settings', function () {
    $user = User::factory()->create(['username' => 'origusername']);
    Profile::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put('/settings/profile', [
        'name' => 'Updated Name',
        'username' => 'newusername',
        'bio' => 'Brand new bio statement',
        'location' => 'Austin, TX',
        'website' => 'https://example.com/me',
    ]);

    $response->assertSessionHas('success');

    $freshUser = $user->fresh();
    expect($freshUser->name)->toBe('Updated Name')
        ->and($freshUser->username)->toBe('newusername')
        ->and($freshUser->profile->bio)->toBe('Brand new bio statement')
        ->and($freshUser->profile->location)->toBe('Austin, TX');
});
