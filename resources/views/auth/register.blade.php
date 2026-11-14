<x-guest-layout :title="__('Create Account — SocialHub')" :subtitle="__('Join the community. Create your account today.')">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Full Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('name') border-rose-500 @enderror"
                placeholder="Sophia Vance">
            @error('name')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username -->
        <div>
            <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Username') }}</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">@</span>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required
                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('username') border-rose-500 @enderror"
                    placeholder="sophiav">
            </div>
            @error('username')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Email Address') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('email') border-rose-500 @enderror"
                placeholder="sophia@example.com">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('password') border-rose-500 @enderror"
                placeholder="{{ __('Minimum 8 characters') }}">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150"
                placeholder="{{ __('Repeat password') }}">
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 transition duration-150 cursor-pointer">
            {{ __('Create Account') }}
        </button>

        <!-- Login Link -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">{{ __('Sign in') }}</a>
            </p>
        </div>
    </form>
</x-guest-layout>
