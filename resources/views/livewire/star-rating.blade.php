<div class="flex items-center gap-1">
    @for($i = 1; $i <= 5; $i++)
        @php $filled = $hovered ? $i <= $hovered : $i <= $rating; @endphp
        <button type="button"
                @if($interactive)
                wire:click="setRating({{ $i }})"
                wire:mouseover="setHovered({{ $i }})"
                wire:mouseleave="clearHovered"
                @endif
                class="focus:outline-none {{ $interactive ? 'cursor-pointer' : 'cursor-default' }}">
            <svg class="w-6 h-6 {{ $filled ? 'text-yellow-400' : 'text-gray-300' }} transition-colors"
                 fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                         0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755
                         1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118
                         l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                         00.951-.69l1.07-3.292z"/>
            </svg>
        </button>
    @endfor

    @if($interactive && $rating)
        <input type="hidden" name="{{ $name }}" value="{{ $rating }}">
    @endif
</div>
