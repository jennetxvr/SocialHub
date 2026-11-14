<x-app-layout :title="__('SocialHub — Settings')">
    <div class="max-w-3xl mx-auto px-4 py-6 space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Settings') }}</h1>
            <p class="text-xs text-slate-500 mt-1">{{ __('Manage your account preferences, profile details, and privacy.') }}</p>
        </div>

        <!-- 1. Edit Profile Form -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm p-6 sm:p-8 space-y-6 ring-1 ring-slate-900/5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">{{ __('Public Profile') }}</h2>

            <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <!-- Avatar and Banner Upload -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Avatar Preview -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">{{ __('Profile Photo') }}</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border border-slate-200 shadow-xs">
                            <input type="file" name="avatar" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                    </div>

                    <!-- Banner Preview -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">{{ __('Header Banner') }}</label>
                        <input type="file" name="banner" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <!-- Name & Username -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Full Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Username') }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">@</span>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                                class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        </div>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Bio') }}</label>
                    <textarea name="bio" rows="3" placeholder="{{ __('Tell the world about yourself...') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 resize-none">{{ old('bio', $user->profile?->bio) }}</textarea>
                </div>

                <!-- Location & Website -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Location') }}</label>
                        <input type="text" name="location" value="{{ old('location', $user->profile?->location) }}" placeholder="{{ __('e.g. San Francisco, CA') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Website') }}</label>
                        <input type="url" name="website" value="{{ old('website', $user->profile?->website) }}" placeholder="https://example.com"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs tracking-wide shadow-sm cursor-pointer transition">
                        {{ __('Save Profile Changes') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- 2. Security / Password Update -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm p-6 sm:p-8 space-y-6 ring-1 ring-slate-900/5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">{{ __('Security & Password') }}</h2>

            <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Current Password') }}</label>
                    <input type="password" name="current_password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('New Password') }}</label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">{{ __('Confirm New Password') }}</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs tracking-wide shadow-sm cursor-pointer transition">
                        {{ __('Update Password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Privacy Settings -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-white/80 shadow-sm p-6 sm:p-8 space-y-6 ring-1 ring-slate-900/5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">{{ __('Account Privacy') }}</h2>

            <form method="POST" action="{{ route('settings.privacy') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_private" value="1" {{ $user->profile?->is_private ? 'checked' : '' }}
                        class="w-4 h-4 mt-1 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <div>
                        <span class="text-sm font-bold text-slate-900">{{ __('Private Account') }}</span>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('When your account is private, only people you approve can see your posts and media.') }}</p>
                    </div>
                </label>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs tracking-wide shadow-sm cursor-pointer transition">
                        {{ __('Save Privacy') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
