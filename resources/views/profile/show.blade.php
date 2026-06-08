<x-layouts.app :title="__('web.profile_title')">

    {{-- Page banner --}}
    <div class="py-10 text-center border-b border-rose-gold/10"
         style="background:linear-gradient(135deg,var(--color-beige) 0%,color-mix(in srgb,var(--color-beige) 30%,white) 60%);">
        <h1 class="text-3xl sm:text-4xl font-bold text-salon-text">{{ __('web.profile_title') }}</h1>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-14">

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if($errors->any())
                <div class="flex items-start gap-2.5 bg-red-50 border border-red-100 text-red-600 text-[13px] px-4 py-3 rounded-xl mb-6">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Avatar section --}}
            <div class="bg-white rounded-2xl p-6 mb-5 flex items-center gap-5"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}"
                         class="w-20 h-20 rounded-full object-cover shrink-0"
                         style="border:3px solid color-mix(in srgb,var(--color-rose-gold) 20%,transparent);">
                @else
                    <div class="w-20 h-20 rounded-full shrink-0 flex items-center justify-center"
                         style="background:linear-gradient(135deg,var(--color-beige),var(--color-soft-pink-light)); border:3px solid color-mix(in srgb,var(--color-rose-gold) 15%,transparent);">
                        <svg class="w-8 h-8 text-rose-gold/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">
                        {{ __('web.avatar') }}
                    </label>
                    <input type="file" name="avatar" accept="image/*"
                           class="text-[13px] text-gray-500 file:mr-3 file:text-[11px] file:font-semibold
                                  file:px-3 file:py-1.5 file:rounded-full file:border file:border-rose-gold/40
                                  file:text-rose-gold file:bg-transparent file:cursor-pointer hover:file:bg-rose-gold/5">
                </div>
            </div>

            {{-- Personal details --}}
            <div class="bg-white rounded-2xl p-6 space-y-5 mb-5"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
                <p class="text-[11px] tracking-[0.18em] uppercase font-bold text-rose-gold/60 -mb-1">
                    {{ app()->getLocale() === 'ar' ? 'المعلومات الشخصية' : 'Personal Information' }}
                </p>

                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">{{ __('web.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="salon-input">
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">{{ __('web.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="salon-input">
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-gray-600 mb-1.5 tracking-wide">{{ __('web.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="salon-input">
                </div>
            </div>

            {{-- Password change --}}
            <div class="bg-white rounded-2xl p-6 space-y-4 mb-6"
                 style="border:1px solid color-mix(in srgb,var(--color-rose-gold) 10%,transparent); box-shadow:0 2px 12px color-mix(in srgb,var(--color-rose-gold) 6%,transparent);">
                <p class="text-[11px] tracking-[0.18em] uppercase font-bold text-rose-gold/60 -mb-1">
                    {{ __('web.change_password') }}
                </p>
                <input type="password" name="password"
                       placeholder="{{ __('web.new_password') }}"
                       class="salon-input">
                <input type="password" name="password_confirmation"
                       placeholder="{{ __('web.confirm_password') }}"
                       class="salon-input">
            </div>

            <button type="submit"
                    class="w-full bg-rose-gold text-white font-semibold py-4 rounded-full hover:bg-rose-gold-dark tracking-wide text-sm"
                    style="box-shadow:0 6px 20px color-mix(in srgb,var(--color-rose-gold) 38%,transparent);">
                {{ __('web.save_changes') }}
            </button>
        </form>
    </div>

</x-layouts.app>
