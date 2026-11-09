<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('forgot password page can be rendered', function () {
    $this->get('/forgot-password')->assertStatus(200);
});

test('password reset link can be requested for existing user', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'forgotme@example.com']);

    $this->post('/forgot-password', ['email' => 'forgotme@example.com'])
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('password reset link fails gracefully for non-existent email', function () {
    Notification::fake();

    $this->post('/forgot-password', ['email' => 'nobody@nowhere.com'])
        ->assertSessionHasErrors('email');

    Notification::assertNothingSent();
});

test('reset password page can be rendered with valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->get("/reset-password/{$token}?email={$user->email}")
        ->assertStatus(200);
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'reset@example.com']);
    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'reset@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect(route('login'))->assertSessionHas('status');

    // New password works
    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});

test('password reset fails with invalid token', function () {
    $user = User::factory()->create(['email' => 'invalid@example.com']);

    $this->post('/reset-password', [
        'token' => 'invalid-token-value',
        'email' => 'invalid@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHasErrors('email');
});

test('password reset fails with mismatched passwords', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'doesnotmatch',
    ])->assertSessionHasErrors('password');
});

test('old password no longer works after successful reset', function () {
    $user = User::factory()->create([
        'email' => 'oldpw@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'oldpw@example.com',
        'password' => 'brandnewpassword',
        'password_confirmation' => 'brandnewpassword',
    ]);

    expect(Hash::check('oldpassword', $user->fresh()->password))->toBeFalse()
        ->and(Hash::check('brandnewpassword', $user->fresh()->password))->toBeTrue();
});

test('existing authentication features still work after adding password reset', function () {
    $user = User::factory()->create([
        'username' => 'stillthere',
        'email' => 'still@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Login still works
    $this->post('/login', [
        'login' => 'still@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('feed'));

    $this->assertAuthenticatedAs($user);

    // Logout still works
    $this->post('/logout');
    $this->assertGuest();

    // Register page still renders
    $this->get('/register')->assertStatus(200);

    // Login page renders and includes Forgot Password link
    $this->get('/login')->assertStatus(200)->assertSee('Forgot Password?');
});

