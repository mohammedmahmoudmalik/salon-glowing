<x-layouts.admin :title="__('web.admin_profile')">

    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('web.admin_profile') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ app()->getLocale() === 'ar' ? 'تعديل معلوماتك الشخصية وكلمة المرور' : 'Edit your personal information and password' }}</p>
    </div>

    <div class="max-w-4xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ── Left: Avatar card (1/3) ── --}}
            <div class="space-y-5">

                {{-- Avatar --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800">{{ __('web.avatar') }}</h2>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-6 py-5 flex flex-col items-center gap-4">

                            {{-- Current avatar --}}
                            <div id="avatar-preview-wrap" class="relative group">
                                @if($user->avatar)
                                    <img id="avatar-preview"
                                         src="{{ Storage::url($user->avatar) }}"
                                         alt="{{ $user->name }}"
                                         class="w-28 h-28 rounded-full object-cover ring-4 ring-beige">
                                @else
                                    <div id="avatar-initial"
                                         class="w-28 h-28 rounded-full bg-beige flex items-center justify-center text-rose-gold font-bold text-4xl ring-4 ring-rose-gold/10">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <img id="avatar-preview" src="" alt=""
                                         class="hidden w-28 h-28 rounded-full object-cover ring-4 ring-beige">
                                @endif
                            </div>

                            {{-- Role badge --}}
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                         {{ $user->hasRole('admin') ? 'bg-rose-gold/10 text-rose-gold' : 'bg-blue-50 text-blue-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                             {{ $user->hasRole('admin') ? 'bg-rose-gold' : 'bg-blue-400' }}"></span>
                                {{ $user->hasRole('admin')
                                    ? (app()->getLocale() === 'ar' ? 'مدير النظام' : 'Administrator')
                                    : (app()->getLocale() === 'ar' ? 'موظفة استقبال' : 'Receptionist') }}
                            </span>

                            {{-- Upload label --}}
                            <label for="avatar-input"
                                   class="w-full flex flex-col items-center gap-1.5 border-2 border-dashed border-gray-200 rounded-xl px-4 py-4 cursor-pointer hover:border-rose-gold/40 hover:bg-beige/30 transition group">
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-rose-gold/50 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span id="avatar-file-label" class="text-xs text-gray-400 group-hover:text-rose-gold/70 transition text-center">
                                    {{ app()->getLocale() === 'ar' ? 'اضغط لتغيير الصورة' : 'Click to change photo' }}
                                </span>
                                <input id="avatar-input" type="file" name="avatar" accept="image/*" class="hidden">
                            </label>

                            {{-- Remove avatar --}}
                            @if($user->avatar)
                                <label class="flex items-center gap-2 cursor-pointer self-start">
                                    <input type="checkbox" name="remove_avatar" value="1"
                                           class="rounded border-gray-300 text-rose-gold focus:ring-rose-gold">
                                    <span class="text-xs text-red-500 hover:text-red-600 transition">
                                        {{ app()->getLocale() === 'ar' ? 'حذف الصورة الحالية' : 'Remove current photo' }}
                                    </span>
                                </label>
                            @endif

                            {{-- Hidden name so avatar-only form has required name --}}
                            <input type="hidden" name="name" value="{{ $user->name }}">
                        </div>

                        <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100">
                            <button type="submit"
                                    class="w-full bg-rose-gold text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                                {{ app()->getLocale() === 'ar' ? 'حفظ الصورة' : 'Save Photo' }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Account info summary --}}
                <div class="bg-white rounded-2xl shadow-sm p-5 space-y-3 text-sm">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                        {{ app()->getLocale() === 'ar' ? 'معلومات الحساب' : 'Account Info' }}
                    </p>
                    <div class="flex items-center justify-between text-gray-500">
                        <span class="text-xs">{{ app()->getLocale() === 'ar' ? 'تاريخ الانضمام' : 'Joined' }}</span>
                        <span class="text-xs font-medium text-gray-700">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-500">
                        <span class="text-xs">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</span>
                        <span class="text-xs font-semibold {{ $user->is_active ? 'text-green-600' : 'text-red-500' }}">
                            {{ $user->is_active
                                ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active')
                                : (app()->getLocale() === 'ar' ? 'معطّل' : 'Inactive') }}
                        </span>
                    </div>
                </div>

            </div>

            {{-- ── Right: Info + Password cards (2/3) ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Personal info form --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">{{ __('web.profile_title') }}</h2>
                            <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'الاسم والبريد الإلكتروني ورقم الجوال' : 'Name, email, and phone number' }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if($errors->any() && !$errors->has('current_password') && !$errors->has('password'))
                            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                            </div>
                        @endif

                        <div class="px-6 py-5 space-y-4">

                            {{-- Name --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    {{ __('web.name') }} <span class="text-rose-gold normal-case">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    {{ __('web.email') }}
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition"
                                       placeholder="{{ app()->getLocale() === 'ar' ? 'example@email.com' : 'example@email.com' }}">
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    {{ __('web.phone') }}
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition"
                                       placeholder="{{ app()->getLocale() === 'ar' ? '05XXXXXXXX' : '05XXXXXXXX' }}">
                            </div>

                        </div>

                        <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                    class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                                {{ __('web.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password form --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-beige flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-rose-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-gray-800">{{ __('web.change_password') }}</h2>
                            <p class="text-xs text-gray-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'استخدم كلمة مرور قوية لا تقل عن 8 أحرف' : 'Use a strong password of at least 8 characters' }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.profile.password') }}" method="POST">
                        @csrf

                        @if($errors->has('current_password') || $errors->has('password'))
                            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
                                @foreach($errors->get('current_password') as $e) <p>{{ $e }}</p> @endforeach
                                @foreach($errors->get('password') as $e) <p>{{ $e }}</p> @endforeach
                            </div>
                        @endif

                        <div class="px-6 py-5 space-y-4">

                            {{-- Current password --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    {{ __('web.current_password') }} <span class="text-rose-gold normal-case">*</span>
                                </label>
                                <input type="password" name="current_password" autocomplete="current-password"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition
                                              @error('current_password') border-red-300 @enderror">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- New password --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ __('web.new_password') }} <span class="text-rose-gold normal-case">*</span>
                                    </label>
                                    <input type="password" name="password" autocomplete="new-password"
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition
                                                  @error('password') border-red-300 @enderror">
                                </div>

                                {{-- Confirm password --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ __('web.confirm_password') }} <span class="text-rose-gold normal-case">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password"
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-gold/20 focus:border-rose-gold transition">
                                </div>
                            </div>

                        </div>

                        <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex justify-end">
                            <button type="submit"
                                    class="bg-rose-gold text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-rose-gold-dark transition">
                                {{ __('web.change_password') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
    (function () {
        var input   = document.getElementById('avatar-input');
        var preview = document.getElementById('avatar-preview');
        var initial = document.getElementById('avatar-initial');
        var lbl     = document.getElementById('avatar-file-label');
        var defLbl  = lbl ? lbl.textContent.trim() : '';

        if (!input) return;

        input.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;

            if (lbl) lbl.textContent = file.name;

            if (preview) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
            if (initial) initial.classList.add('hidden');
        });
    }());
    </script>

</x-layouts.admin>
