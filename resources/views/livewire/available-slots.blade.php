<div>
    @if(empty($slots))
        <p class="text-sm text-gray-400 italic">{{ __('web.no_slots_available') }}</p>
    @else
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
            @foreach($slots as $slot)
                <button type="button"
                        wire:click="selectSlot('{{ $slot }}')"
                        class="py-2 px-3 text-sm rounded-lg border transition font-medium
                               {{ $selected === $slot
                                  ? 'bg-rose-gold text-white border-rose-gold'
                                  : 'bg-white text-gray-700 border-gray-200 hover:border-rose-gold hover:text-rose-gold' }}">
                    {{ $slot }}
                </button>
            @endforeach
        </div>
    @endif
</div>
