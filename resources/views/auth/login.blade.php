<x-layouts.guest :title="__('web.login_title')">

    <h2 class="text-xl font-bold text-salon-text mb-1 text-center tracking-tight">
        {{ __('web.login_title') }}
    </h2>
    <p class="text-[12px] text-gray-400 text-center mb-7 tracking-wide">
        {{ app()->getLocale() === 'ar' ? 'مرحباً بعودتك' : 'Welcome back' }}
    </p>

    <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
        @csrf

        @if($errors->any())
            <div class="flex items-start gap-2.5 bg-red-50 border border-red-100 text-red-600 text-[13px] px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">
                {{ __('web.login_field') }}
            </label>
            <input type="text" name="login" value="{{ old('login') }}"
                   required autofocus autocomplete="username"
                   class="salon-input">
        </div>

        <div>
            <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">
                {{ __('web.password') }}
            </label>
            <input type="password" name="password"
                   required autocomplete="current-password"
                   class="salon-input">
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 text-[13px] text-gray-500 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="rounded border-gray-300 text-rose-gold focus:ring-rose-gold">
                {{ __('web.remember_me') }}
            </label>
        </div>

        <button type="submit"
                class="w-full bg-rose-gold text-white font-semibold py-3.5 rounded-full hover:bg-rose-gold-dark tracking-wide text-sm mt-2"
                style="box-shadow:0 5px 18px color-mix(in srgb,var(--color-rose-gold) 38%,transparent);">
            {{ __('web.sign_in') }}
        </button>
    </form>

    <p class="text-center text-[13px] text-gray-400 mt-6">
        {{ __('web.no_account') }}
        <a href="{{ route('register') }}" class="text-rose-gold font-semibold hover:text-rose-gold-dark">
            {{ __('web.sign_up') }}
        </a>
    </p>

</x-layouts.guest>
