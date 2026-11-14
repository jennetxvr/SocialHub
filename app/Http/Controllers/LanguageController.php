<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function __invoke(string $locale, Request $request): RedirectResponse
    {
        abort_unless(in_array($locale, config('app.supported_locales'), true), 404);

        $request->session()->put('locale', $locale);

        $user = $request->user();

        if ($user instanceof User) {
            $user->update(['preferred_locale' => $locale]);
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return back();
    }
}
