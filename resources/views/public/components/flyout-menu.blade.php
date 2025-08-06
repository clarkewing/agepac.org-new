@props([
    'align' => 'left',
    'flyoutClasses' => 'max-w-xs',
])

@php
    switch ($align) {
        case 'left':
            $alignmentClasses = '-ml-4';
            break;
        case 'center':
            $alignmentClasses = 'left-1/2 -translate-x-1/2';
            break;
        case 'right':
        default:
            $alignmentClasses = 'right-0 -mr-4';
            break;
    }
@endphp


<div class="flex justify-center">
    <div
        x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.$refs.button.focus()

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }"
        x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        x-id="['dropdown-button']"
        class="relative"
    >
        <!-- Button -->
        <button
            x-ref="button"
            x-on:click="toggle()"
            :aria-expanded="open"
            :aria-controls="$id('dropdown-button')"
            type="button"
            @class([
                'rounded-md inline-flex items-center text-base font-medium focus:outline-hidden',
                ...array_map(fn ($c) => 'hover:' . $c, explode(' ', $trigger->attributes->get('active-class') ?? 'text-gray-900'))
            ])
            :class="{'{{ $trigger->attributes->get('active-class') ?? 'text-gray-900' }}': open, '{{ $trigger->attributes->get('class') ?? 'text-gray-500' }}': ! open }"
        >
            <span class="whitespace-nowrap">{{ $trigger }}</span>
            <x-heroicon-s-chevron-down class="ml-1 h-5 w-5" />
        </button>

        <!-- Panel -->
        <div
            x-ref="panel"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            x-on:click.outside="close($refs.button)"
            :id="$id('dropdown-button')"
            style="display: none;"
            class="absolute z-10 {{ $alignmentClasses }} mt-3 w-screen {{ $flyoutClasses }} px-4 md:px-0"
        >
            <div class="rounded-lg shadow-lg ring-1 ring-black/5 overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
