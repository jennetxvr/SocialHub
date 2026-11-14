<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

uses(RefreshDatabase::class);

it('uses English for first-time visitors', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('Sign In — SocialHub');

    expect(app()->getLocale())->toBe('en')
        ->and(session('locale'))->toBe('en');
});

it('saves a supported locale for guests across requests', function (string $locale, string $translatedText) {
    $this->from('/login')
        ->post(route('language.switch', $locale))
        ->assertRedirect('/login')
        ->assertSessionHas('locale', $locale);

    $this->get('/login')
        ->assertOk()
        ->assertSee($translatedText);

    expect(app()->getLocale())->toBe($locale);
})->with([
    'Turkish' => ['tr', 'Giriş Yap — SocialHub'],
    'Russian' => ['ru', 'Войти — SocialHub'],
]);

it('rejects unsupported locales', function () {
    $this->post('/language/fr')->assertNotFound();

    expect(session('locale'))->toBe('en');
});

it('falls back to English when the session contains an unsupported locale', function () {
    $this->withSession(['locale' => 'fr'])
        ->get('/login')
        ->assertOk()
        ->assertSee('Sign In — SocialHub')
        ->assertSessionHas('locale', 'en');

    expect(app()->getLocale())->toBe('en');
});

it('persists locale selection for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/settings')
        ->post(route('language.switch', 'tr'))
        ->assertRedirect('/settings')
        ->assertSessionHas('locale', 'tr');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'preferred_locale' => 'tr',
    ]);

    $this->get('/settings')
        ->assertOk()
        ->assertSee('Ayarlar');

    expect(app()->getLocale())->toBe('tr');
});

it('restores a user language preference on login', function () {
    $user = User::factory()->create([
        'preferred_locale' => 'ru',
        'password' => Hash::make('password'),
    ]);

    $this->post('/login', [
        'login' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('feed'));

    $this->get('/')
        ->assertOk()
        ->assertSee('Главная');

    expect(app()->getLocale())->toBe('ru')
        ->and(session('locale'))->toBe('ru');
});

it('preserves the selected locale after logout', function () {
    $user = User::factory()->create([
        'preferred_locale' => 'tr',
    ]);

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'))
        ->assertSessionHas('locale', 'tr');

    $this->get('/login')
        ->assertOk()
        ->assertSee('Giriş Yap — SocialHub');

    expect(app()->getLocale())->toBe('tr');
});

it('falls back to English for a missing translation', function () {
    Lang::addLines(['language.fallback_probe' => 'English fallback'], 'en');
    App::setLocale('tr');

    expect(__('language.fallback_probe'))->toBe('English fallback');

    App::setLocale('en');
});

it('renders validation messages in the selected language', function () {
    $this->withSession(['locale' => 'tr'])
        ->post('/register', [])
        ->assertSessionHasErrors([
            'name' => 'ad soyad alanı zorunludur.',
        ]);
});

it('localizes dates, relative times, and clock times', function () {
    $this->withSession(['locale' => 'tr'])
        ->get('/login')
        ->assertOk();

    $date = now()->setDate(2026, 3, 4)->setTime(13, 5);

    expect($date->translatedFormat('F Y'))->toBe('Mart 2026')
        ->and(now()->subMinutes(2)->diffForHumans())->toBe('2 dakika önce')
        ->and($date->locale('tr')->isoFormat('LT'))->toBe('13:05');
});
