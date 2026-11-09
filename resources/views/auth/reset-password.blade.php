<x-guest-layout title="Reset Password — SocialHub" subtitle="Choose a new password for your account.">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('email') border-rose-500 @enderror"
                placeholder="name@example.com">
            @error('email')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">New Password</label>
            <input id="password" type="password" name="password" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('password') border-rose-500 @enderror"
                placeholder="Minimum 8 characters">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150"
                placeholder="Repeat new password">
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 transition duration-150 cursor-pointer">
            Reset Password
        </button>

        <!-- Back to Login -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">
                <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">Back to Sign In</a>
            </p>
        </div>
    </form>
</x-guest-layout>

