<x-layouts.app :title="__('web.reschedule')">

    <div class="max-w-lg mx-auto px-4 sm:px-6 py-12">
        <h1 class="text-2xl font-bold text-salon-text mb-6">{{ __('web.reschedule') }}</h1>

        <div class="bg-beige rounded-xl p-4 mb-6 text-sm text-gray-600 space-y-1">
            <p><span class="font-medium">{{ __('web.date') }}:</span> {{ $booking->booking_date }}</p>
            <p><span class="font-medium">{{ __('web.time') }}:</span> {{ $booking->start_time }}</p>
        </div>

        <form action="{{ route('bookings.reschedule', $booking) }}" method="POST" class="space-y-5 bg-white rounded-2xl shadow-sm p-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.select_date') }}</label>
                <input type="date" name="booking_date" value="{{ old('booking_date') }}"
                       min="{{ now()->toDateString() }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-gold">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('web.select_time') }}</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-gold">
            </div>

            <button type="submit"
                    class="w-full bg-rose-gold text-white font-semibold py-3 rounded-xl hover:bg-rose-gold-dark transition">
                {{ __('web.confirm_booking') }}
            </button>
        </form>
    </div>

</x-layouts.app>
