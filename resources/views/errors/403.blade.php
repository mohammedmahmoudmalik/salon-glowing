<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('web.forbidden') }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-beige min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <div class="text-8xl font-bold text-rose-gold mb-4">403</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('web.forbidden') }}</h1>
        <p class="text-gray-500 mb-8">{{ __('web.forbidden_desc') }}</p>
        <div class="flex items-center justify-center gap-4">
            <a href="{{ route('home') }}"
               class="inline-block bg-rose-gold text-white font-semibold px-6 py-3 rounded-xl hover:bg-rose-gold-dark transition">
                {{ __('web.go_home') }}
            </a>
            @auth
                <a href="{{ route('bookings.index') }}"
                   class="inline-block border border-rose-gold text-rose-gold font-semibold px-6 py-3 rounded-xl hover:bg-rose-gold hover:text-white transition">
                    {{ __('web.my_bookings') }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-block border border-rose-gold text-rose-gold font-semibold px-6 py-3 rounded-xl hover:bg-rose-gold hover:text-white transition">
                    {{ __('web.login') }}
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
