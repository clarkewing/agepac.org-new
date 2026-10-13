@blaze

@use('Illuminate\Support\Number')

{{--
    Counts down to a moment on the browser's clock (see the "countdown"
    Alpine component in resources/js/app.js). The default slot shows while
    counting, with any :countdown placeholder in it kept live as a rounded-up
    duration; the "done" slot shows once the moment has passed.
--}}

@props([
    'until',
])

@php
    $remaining = max(0, $until - now()->getTimestamp());
    $countdown = '<span x-text="countdown()">'.e(Number::duration($remaining)).'</span>';
@endphp

<span {{ $attributes }} x-data="countdown({ until: {{ $until }}, locale: '{{ app()->getLocale() }}' })">
    <span x-show="remaining > 0" x-cloak>{!! str_replace(':countdown', $countdown, $slot) !!}</span>

    @isset($done)
        <span x-show="remaining === 0" x-cloak>{{ $done }}</span>
    @endisset
</span>
