<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register and get a default profile', function () {
    $response = $this->post('/register', [
        'name' => 'Alice Walker',
        'username' => 'alicew',
        'email' => 'alice@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('feed'));

    $user = User::where('email', 'alice@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->username)->toBe('alicew')
        ->and($user->profile)->not->toBeNull();
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using email or username', function () {
    $user = User::factory()->create([
        'username' => 'testuser',
        'email' => 'testuser@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Authenticate with email
    $responseEmail = $this->post('/login', [
        'login' => 'testuser@example.com',
        'password' => 'password123',
    ]);
    $responseEmail->assertRedirect(route('feed'));
    $this->assertAuthenticatedAs($user);

    // Logout
    $this->post('/logout');
    $this->assertGuest();

    // Authenticate with username
    $responseUser = $this->post('/login', [
        'login' => 'testuser',
        'password' => 'password123',
    ]);
    $responseUser->assertRedirect(route('feed'));
    $this->assertAuthenticatedAs($user);
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'wrong@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    $this->post('/login', [
        'login' => 'wrong@example.com',
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
