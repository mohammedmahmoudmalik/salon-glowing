<x-layouts.app :title="__('web.review_title')">

    <div class="max-w-lg mx-auto px-4 sm:px-6 py-12">
        <h1 class="text-2xl font-bold text-salon-text mb-6">{{ __('web.review_title') }}</h1>

        <form action="{{ route('bookings.review.store', $booking) }}" method="POST"
              class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('web.review_rating') }}</label>
                <div class="flex gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                            <svg class="w-8 h-8 text-gray-200 hover:text-yellow-400 transition peer-checked:text-yellow-400"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    @endfor
                </div>
                @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.review_comment') }}</label>
                <textarea name="comment" rows="4"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-gold resize-none">{{ old('comment') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-rose-gold text-white font-semibold py-3 rounded-xl hover:bg-rose-gold-dark transition">
                {{ __('web.submit_review') }}
            </button>
        </form>
    </div>

</x-layouts.app>
