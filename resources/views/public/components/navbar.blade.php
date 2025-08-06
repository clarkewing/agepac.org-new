@props(['branding' => 'logo'])

@php($isOverlayed = $attributes->has('overlay'))

<div
    @class([
        'relative',
        'bg-white' => ! $isOverlayed,
    ])
>
    <div class="max-w-7xl mx-auto flex justify-between items-center px-4 py-6 sm:px-6 md:justify-start md:space-x-6 lg:space-x-10">
        <div>
            <a href="{{ route('public.home') }}" class="flex">
                <span class="sr-only">AGEPAC</span>
                @if($branding === 'logo')
                    <x-public::application-logo class="h-8 w-auto sm:h-10 {{ $isOverlayed ? 'text-white' : '' }}" />
                @elseif($branding === 'mark')
                    <x-public::application-mark class="h-8 w-auto sm:h-10 {{ $isOverlayed ? 'text-white' : '' }}" />
                @endif
            </a>
        </div>
        <div class="-mr-2 -my-2 md:hidden">
            <x-public::mobile-nav>
                <x-slot name="trigger">
                    <button
                        type="button"
                        @class([
                            'rounded-md p-2 inline-flex items-center justify-center focus:outline-hidden focus:ring-2 focus:ring-inset focus:ring-blue-500',
                            'text-gray-400 hover:text-gray-500 hover:bg-gray-100' => ! $isOverlayed,
                            'text-white hover:text-white/75' => $isOverlayed,
                        ])
                    >
                        <span class="sr-only">Open menu</span>
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </button>
                </x-slot>
            </x-public::mobile-nav>
        </div>
        <div class="hidden md:flex-1 md:flex md:items-center md:justify-between">
            <nav class="flex space-x-6 lg:space-x-10">
                <x-public::flyout-menu
                    align="center"
                    flyout-classes="max-w-md"
                >
                    <x-slot
                        name="trigger"
                        class="{{ $isOverlayed ? 'text-white' : 'text-gray-500' }}"
                        active-class="{{ $isOverlayed ? 'text-white/75' : 'text-gray-900' }}"
                    >
                        <span>Être EPL</span>
                    </x-slot>

                    <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                        <a href="{{ route('public.epl.selection') }}" class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50 transition ease-in-out duration-150">
                            <x-heroicon-o-users class="shrink-0 h-6 w-6 text-wedgewood-500" />
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                    La Sélection
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Découvrez les différentes voies d’accès à la filière EPL.
                                </p>
                            </div>
                        </a>

                        <a href="{{ route('public.epl.training') }}" class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50 transition ease-in-out duration-150">
                            <x-heroicon-o-academic-cap class="shrink-0 h-6 w-6 text-wedgewood-500" />
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                    La Formation
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Apprenez-en plus sur le cursus EPL et les différentes phases qu’il comporte.
                                </p>
                            </div>
                        </a>
                    </div>
                    <div class="px-5 py-5 bg-gray-50 space-y-6 sm:flex sm:space-y-0 sm:space-x-10 sm:px-8">
                        <div class="flow-root">
                            <a
                                href="https://youtu.be/g8d4rvT29z8"
                                target="_blank"
                                class="-m-3 p-3 flex items-center rounded-md text-base font-medium text-gray-900 hover:bg-gray-100 transition ease-in-out duration-150"
                            >
                                <x-heroicon-o-play class="shrink-0 h-6 w-6 text-gray-400" />
                                <span class="ml-3">Présentation</span>
                            </a>
                        </div>

                        <div class="flow-root">
                            <a href="https://www.enac.fr" target="_blank" class="-m-3 p-3 flex items-center rounded-md text-base font-medium text-gray-900 hover:bg-gray-100 transition ease-in-out duration-150">
                                <x-heroicon-o-arrow-top-right-on-square class="shrink-0 h-6 w-6 text-gray-400" />
                                <span class="ml-3">Découvrir l’ENAC</span>
                            </a>
                        </div>
                    </div>
                </x-public::flyout-menu>

                <x-public::flyout-menu
                    align="center"
                    flyout-classes="max-w-md"
                >
                    <x-slot
                        name="trigger"
                        class="{{ $isOverlayed ? 'text-white' : 'text-gray-500' }}"
                        active-class="{{ $isOverlayed ? 'text-white/75' : 'text-gray-900' }}"
                    >
                        <span>Association</span>
                    </x-slot>

                    <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                        <a href="{{ route('public.association.about') }}" class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50 transition ease-in-out duration-150">
                            <x-heroicon-o-building-library class="shrink-0 h-6 w-6 text-wedgewood-500" />
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                    Qui sommes-nous ?
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Qu’est-ce que l’AGEPAC ? Découvrez notre association et ses valeurs.
                                </p>
                            </div>
                        </a>

                        <a href="{{ route('public.association.team') }}" class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50 transition ease-in-out duration-150">
                            <x-heroicon-o-user-group class="shrink-0 h-6 w-6 text-wedgewood-500" />
                            <div class="ml-4">
                                <p class="text-base font-medium text-gray-900">
                                    Notre équipe
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Rencontrez les membres du Bureau et du Conseil de l’AGEPAC.
                                </p>
                            </div>
                        </a>
                    </div>
                </x-public::flyout-menu>

                <a
                    href="{{ route('public.contact') }}"
                    @class([
                        'text-base font-medium',
                        'text-gray-500 hover:text-gray-900' => ! $isOverlayed,
                        'text-white hover:text-white/75' => $isOverlayed,
                    ])
                >
                    Contact
                </a>
            </nav>

            @unless($attributes->has('compact'))
                <div class="flex items-center md:ml-12">
                    <a
                        href="{{ route('dashboard') }}"
                        @class([
                            'w-full border border-transparent rounded-full lg:rounded-md p-1 lg:py-2 lg:px-4 flex items-center justify-center text-base focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-vermilion-400',
                            'text-vermilion-400 lg:text-white lg:bg-vermilion-400 hover:text-vermilion-500 lg:hover:text-white lg:hover:bg-vermilion-500' => ! $isOverlayed,
                            'text-white hover:text-white/75 lg:bg-white/10 lg:hover:bg-white/20' => $isOverlayed,
                        ])
                    >
                        <x-heroicon-o-user-circle class="shrink-0 h-7 w-7 lg:h-5 lg:w-5" />
                        <span class="sr-only lg:not-sr-only lg:ml-2">Espace Membres</span>
                    </a>
                </div>
            @endunless
        </div>
    </div>
</div>
