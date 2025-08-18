<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if($attributes->has('title'))
            <title>{{ $attributes->get('title') . ' | AGEPAC' }}</title>
        @else
            <title>AGEPAC – The ENAC Pilot Association</title>
        @endif

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <meta name="theme-color" content="rgb(0, 10, 51)">

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        @vite(['resources/css/public.css', 'resources/js/public.js'])

        <script
            defer
            src='https://static.cloudflareinsights.com/beacon.min.js'
            data-cf-beacon='{"token": "9318628569ea49d3a823ea8f52a9b673"}'
        ></script>
    </head>
    <body class="font-sans antialiased">
        <x-public::banner />

        <div class="min-h-screen bg-gray-100">
            @isset($header)
                @if($header->attributes->has('raw'))
                    <header {{ $header->attributes->merge(['class' => 'bg-white']) }}>
                        {{ $header }}
                    </header>
                @elseif($header->attributes->has('backdrop'))
                    <header class="relative pb-24 bg-wedgewood-500 sm:pb-32">
                        <div class="absolute inset-0">
                            <img
                                class="w-full h-full object-cover saturate-0"
                                src="{{ $header->attributes->get('backdrop') }}"
                                alt="{{ $header->attributes->get('alt') }}"
                            />
                            <div class="absolute inset-0 bg-linear-to-l from-wedgewood-500 to-cyan-700 mix-blend-multiply" aria-hidden="true"></div>
                        </div>

                        <!-- Navigation bar -->
                        <x-public::navbar overlay />

                        <!-- Page Heading -->
                        <div class="relative mt-24 max-w-md mx-auto px-4 sm:max-w-3xl sm:mt-32 sm:px-6 lg:max-w-7xl lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @else
                    <header class="bg-white">
                        <!-- Navigation bar -->
                        <x-public::navbar />

                        <!-- Page Heading -->
                        <div {{ $header->attributes->merge(['class' => 'max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8']) }}>
                            {{ $header }}
                        </div>
                    </header>
                @endif
            @else
                <header class="bg-white">
                    <!-- Navigation bar -->
                    <x-public::navbar />
                </header>
            @endisset

            <!-- Page Content -->
            <main {{ $attributes->except('title') }}>
                {{ $slot }}
            </main>

            <x-public::footer />
        </div>
    </body>
</html>
