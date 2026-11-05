<x-guest-layout title="Sign In — SocialHub" subtitle="Welcome back! Please enter your details.">
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Username or Email -->
        <div>
            <label for="login" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email or Username</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('login') border-rose-500 @enderror"
                placeholder="name@example.com or @username">
            @error('login')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
            </div>
            <input id="password" type="password" name="password" required
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition duration-150 @error('password') border-rose-500 @enderror"
                placeholder="••••••••">
            @error('password')
                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between py-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                <span class="text-xs text-slate-600 font-medium">Remember me</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 transition duration-150 cursor-pointer">
            Sign In
        </button>

        <!-- Register Link -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500">
                Don't have an account yet?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">Create an account</a>
            </p>
        </div>
    </form>
</x-guest-layout>

