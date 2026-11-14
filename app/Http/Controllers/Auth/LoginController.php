<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) { // /checks login status, Ulanyjynyň ulgama girendigini barlaýar
            return redirect()->route('feed');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attemptData = [
            $loginField => strtolower($credentials['login']),
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($attemptData, $remember)) { // authenticates user credentials,  ulanyjy ady we paroly tassyklap, ulgama girizýär. girizilen maglumatlari MYSQLdaki database bilen denesdiryar\barlayar
            /** @var User $user */
            $user = Auth::user();
            $locale = $user->preferred_locale ?? $request->session()->get('locale');

            if (! in_array($locale, config('app.supported_locales'), true)) {
                $locale = config('app.fallback_locale', 'en');
            }

            if ($user->preferred_locale !== $locale) {
                $user->update(['preferred_locale' => $locale]);
            }

            $request->session()->put('locale', $locale);
            App::setLocale($locale);
            Carbon::setLocale($locale);
            $request->session()->regenerate();

            return redirect()->intended(route('feed'))->with('success', __('Welcome back!'));
        }

        return back()->withErrors([
            'login' => __('The provided credentials do not match our records.'),
        ])->onlyInput('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        $locale = $request->session()->get('locale', config('app.fallback_locale', 'en'));

        if (! in_array($locale, config('app.supported_locales'), true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->put('locale', $locale);
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', __('You have been logged out.'));
    }
}
