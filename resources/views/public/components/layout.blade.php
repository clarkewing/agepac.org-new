<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @head

    @fonts('inter')

    @vite(['resources/css/public.css', 'resources/js/public.js'])

    @include('partials.analytics')
</head>
<body class="font-sans antialiased">
    <x-public::banner />

    <div class="min-h-screen bg-gray-100">
        @isset($header)
            @if ($header->attributes->has('raw'))
                <header {{ $header->attributes->merge(['class' => 'bg-white']) }}>{{ $header }}</header>
            @elseif ($header->attributes->has('backdrop'))
                <header class="bg-wedgewood-500 relative pb-24 sm:pb-32">
                    <div class="absolute inset-0">
                        <img
                            class="h-full w-full object-cover saturate-0"
                            src="{{ $header->attributes->get('backdrop') }}"
                            alt="{{ $header->attributes->get('alt') }}"
                        />
                        <div
                            class="from-wedgewood-500 absolute inset-0 bg-linear-to-l to-cyan-700 mix-blend-multiply"
                            aria-hidden="true"
                        ></div>
                    </div>

                    <!-- Navigation bar -->
                    <x-public::navbar overlay />

                    <!-- Page Heading -->
                    <div class="relative mx-auto mt-24 max-w-md px-4 sm:mt-32 sm:max-w-3xl sm:px-6 lg:max-w-7xl lg:px-8">
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
        <main {{ $attributes }}>{{ $slot }}</main>

        <x-public::footer />
    </div>
</body>
</html>
