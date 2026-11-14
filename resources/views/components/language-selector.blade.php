@props(['variant' => 'desktop'])

@php
    $languages = [
        'en' => ['flag' => '🇬🇧', 'label' => 'English'],
        'tr' => ['flag' => '🇹🇷', 'label' => 'Türkçe'],
        'ru' => ['flag' => '🇷🇺', 'label' => 'Русский'],
    ];
    $currentLocale = app()->getLocale();
    $currentLanguage = $languages[$currentLocale] ?? $languages['en'];
    $dropdownPosition = $variant === 'mobile' ? 'right-0 top-full mt-2' : 'bottom-full left-0 mb-2';
@endphp

<div class="relative {{ $variant === 'mobile' ? '' : 'mb-3' }}">
    <details class="group">
        <summary
            @if ($variant === 'mobile')
                aria-label="{{ __('Change language') }}"
                class="list-none cursor-pointer rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-indigo-600"
            @else
                class="list-none flex w-full cursor-pointer items-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-indigo-600"
            @endif
        >
            @if ($variant === 'mobile')
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke-width="2" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" />
                </svg>
            @else
                <span aria-hidden="true">🌐</span>
                <span>{{ $currentLanguage['flag'] }} {{ __($currentLanguage['label']) }}</span>
            @endif
        </summary>

        <div class="absolute {{ $dropdownPosition }} z-50 min-w-40 rounded-xl border border-slate-200/70 bg-white/95 p-1.5 shadow-lg backdrop-blur-xl">
            @foreach ($languages as $locale => $language)
                <form method="POST" action="{{ route('language.switch', $locale) }}">
                    @csrf
                    <button
                        type="submit"
                        @if ($currentLocale === $locale) aria-current="true" @endif
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs font-medium text-slate-600 hover:bg-slate-100 hover:text-indigo-600"
                    >
                        <span aria-hidden="true">{{ $language['flag'] }}</span>
                        <span>{{ __($language['label']) }}</span>
                    </button>
                </form>
            @endforeach
        </div>
    </details>
</div>
