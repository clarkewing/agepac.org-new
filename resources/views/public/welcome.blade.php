@use(Laravel\Head\Facades\Head)
@use(Laravel\Head\Facades\Schema)

@php
    Head::title('AGEPAC – The ENAC Pilot Association', exact: true)
        ->description($description = 'L’AGEPAC est l’Association Générale des Élèves Pilotes de l’Aviation Civile, l’association des Élèves Pilotes de Ligne issus de la formation ENAC.')
        ->og(url: route('public.home'))
        ->ogImage(asset('media/cockpit-ifr-leana.jpg'), alt: 'Une EPL en cours de navigation IFR en TB20')
        ->schema(Schema::organization()
            ->name('AGEPAC')
            ->legalName('Association Générale des Élèves Pilotes de l’Aviation Civile')
            ->description($description)
            ->url(route('public.home'))
            ->logo(asset('icon-512.png'))
            ->email('bonjour@agepac.org')
            ->foundingDate('1992')
            ->address(Schema::make('PostalAddress')
                ->streetAddress('7 avenue Edouard Belin')
                ->postOfficeBoxNumber('BP 54005')
                ->postalCode('31055')
                ->addressLocality('Toulouse Cedex 4')
                ->addressCountry('FR'))
            ->contactPoint([
                Schema::make('ContactPoint')->contactType('media relations')->email('media@agepac.org'),
                Schema::make('ContactPoint')->contactType('recruitment')->email('recruitment@agepac.org'),
                Schema::make('ContactPoint')->contactType('partnerships')->email('partners@agepac.org'),
                Schema::make('ContactPoint')->contactType('social media')->email('social@agepac.org'),
            ])
            ->sameAs([
                'https://twitter.com/agepac',
                'https://www.instagram.com/epl_agepac/',
                'https://www.linkedin.com/company/agepac/',
            ]));
@endphp

<x-public::layout>
    <x-slot name="header" class="relative overflow-hidden bg-white" raw>
        <div class="mx-auto max-w-7xl">
            <div class="relative z-10 bg-white pb-8 sm:pb-16 md:pb-20 lg:w-full lg:max-w-2xl lg:pb-28 xl:pb-32">
                <!-- Slanted cutaway -->
                <svg class="absolute inset-y-0 right-0 hidden h-full w-48 translate-x-1/2 text-white lg:block" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>

                <x-public::navbar branding="mark" compact />

                <main class="mx-auto mt-10 max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="text-universe block xl:inline">L’association des</span>
                            <span class="text-wedgewood-500 block xl:inline">Élèves Pilotes de Ligne</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mx-auto sm:mt-5 sm:max-w-xl sm:text-lg md:mt-5 md:text-xl lg:mx-0">
                            L’AGEPAC est l’Association Générale des Élèves Pilotes de l’Aviation Civile. Cette
                            association de loi 1901 est constituée par les Élèves Pilotes de Ligne (EPL) issus de la
                            formation ENAC.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow-sm">
                                <a
                                    href="{{ route('public.association.about') }}"
                                    class="bg-wedgewood-600 hover:bg-wedgewood-700 flex w-full items-center justify-center rounded-md border border-transparent px-8 py-3 text-base font-medium text-white md:px-10 md:py-4 md:text-lg"
                                >
                                    En savoir plus
                                </a>
                            </div>
                            {{--                            <div class="mt-3 sm:mt-0 sm:ml-3">--}}
                            {{--                                <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-shiraz-700 bg-shiraz-100 hover:bg-shiraz-200 md:py-4 md:text-lg md:px-10">--}}
                            {{--                                    Nous recruter--}}
                            {{--                                </a>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img
                class="h-56 w-full object-cover sm:h-72 md:h-96 lg:h-full lg:w-full"
                src="{{ asset('media/cockpit-ifr-leana.jpg') }}"
                alt="Une EPL en cours de navigation IFR en TB20"
            />
        </div>
    </x-slot>

    <!-- Members area CTA -->
    <div class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:flex lg:items-center lg:justify-between lg:px-8 lg:py-16">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <span class="block">Ancien élève ? Encore en formation ?</span>
                <span class="text-wedgewood-600 block">Découvrez notre espace membres !</span>
            </h2>
            <div class="mt-8 flex lg:mt-0 lg:shrink-0">
                <div class="inline-flex rounded-md shadow-sm">
                    <a
                        href="{{ route('dashboard') }}"
                        class="bg-wedgewood-600 hover:bg-wedgewood-700 inline-flex items-center justify-center rounded-md border border-transparent px-5 py-3 text-base font-medium text-white"
                    >
                        Rejoignez-nous
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="relative overflow-hidden bg-white">
        <x-public::pattern.dots-on-solid class="absolute top-0 right-1/2 hidden -translate-x-64 -translate-y-8 lg:block" />

        <!-- Selection & training teaser -->
        <div class="relative py-16 sm:py-24 lg:py-32">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                    <div class="sm:text-center md:mx-auto md:max-w-2xl lg:col-span-6 lg:text-left">
                        <h1>
                            <span class="mt-1 block text-4xl font-extrabold tracking-tight sm:text-5xl xl:text-6xl">
                                <span class="text-universe block xl:inline">Des pilotes</span>
                                <span class="text-vermilion-400 block xl:inline">formés pour exceller</span>
                            </span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                            Les EPL sont sélectionnés suite à un concours annuel attirant plusieurs milliers de
                            candidats. À travers une sélection aussi rigoureuse, l’ENAC s'assure que les élèves auront
                            un niveau minimal dès l’entrée dans le cursus EPL reconnu mondialement comme l’un des plus
                            exigeants.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow-sm">
                                <a
                                    href="{{ route('public.epl.training') }}"
                                    class="bg-vermilion-400 hover:bg-vermilion-500 flex w-full items-center justify-center rounded-md border border-transparent px-8 py-3 text-base font-medium text-white md:px-10 md:py-4 md:text-lg"
                                >
                                    La Formation
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a
                                    href="{{ route('public.epl.selection') }}"
                                    class="text-vermilion-700 bg-vermilion-100 hover:bg-vermilion-200 flex w-full items-center justify-center rounded-md border border-transparent px-8 py-3 text-base font-medium md:px-10 md:py-4 md:text-lg"
                                >
                                    Le Concours
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-12 sm:mx-auto sm:max-w-lg lg:order-first lg:col-span-6 lg:mx-0 lg:mt-0 lg:flex lg:max-w-none lg:items-center">
                        {{--                        <x-public::pattern.dots-on-solid class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-8 scale-75 origin-top sm:scale-100 lg:hidden" />--}}

                        <div class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md">
                            {{--                            <button type="button" class="relative block w-full bg-white rounded-lg overflow-hidden focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500">--}}
                            <div class="relative block w-full overflow-hidden rounded-lg bg-white">
                                {{--                                <span class="sr-only">Watch our video to learn more</span>--}}
                                <img
                                    class="w-full"
                                    src="{{ asset('media/flightline.jpg') }}"
                                    alt="Vue des dérives de nombreux TB20 et d'un CAP10 de l'ENAC"
                                />
                                {{--                                <div class="absolute inset-0 w-full h-full flex items-center justify-center" aria-hidden="true">--}}
                                {{--                                    <svg class="h-20 w-20 text-wedgewood-500 fill-current" viewBox="0 0 84 84">--}}
                                {{--                                        <circle opacity="0.9" cx="42" cy="42" r="42" fill="white" />--}}
                                {{--                                        <path d="M55.5039 40.3359L37.1094 28.0729C35.7803 27.1869 34 28.1396 34 29.737V54.263C34 55.8604 35.7803 56.8131 37.1094 55.9271L55.5038 43.6641C56.6913 42.8725 56.6913 41.1275 55.5039 40.3359Z" />--}}
                                {{--                                    </svg>--}}
                                {{--                                </div>--}}
                                {{--                            </button>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recruitment teaser -->
        <div class="relative">
            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gray-100"></div>
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="relative shadow-xl sm:overflow-hidden sm:rounded-2xl">
                    <div class="absolute inset-0">
                        <img
                            class="h-full w-full object-cover object-top"
                            src="{{ asset('media/big-group-be58.jpg') }}"
                            alt="Large group of EPLs posing in front of a Beechcraft Baron 58"
                        />
                        <div class="bg-wedgewood-700 absolute inset-0" style="mix-blend-mode: multiply"></div>
                    </div>
                    <div class="relative px-4 py-16 sm:px-6 sm:py-24 lg:px-8 lg:py-32">
                        <h1 class="text-center text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                            <span class="block text-white">Prêts à contribuer à</span>
                            <span class="text-vermilion-400 block">votre opération</span>
                        </h1>
                        <p class="mx-auto mt-6 max-w-lg text-center text-xl text-gray-200 sm:max-w-3xl">
                            Les EPL sont extrêmement polyvalents. Formés pour évoluer dans des cockpits de toute taille,
                            ils sont capables de travailler efficacement en travail aérien, comme en compagnie aérienne.
                        </p>
                        <div class="mx-auto mt-10 max-w-sm sm:flex sm:max-w-none sm:justify-center">
                            <div class="space-y-4 sm:mx-auto">
                                <a
                                    href="mailto:recruitment@agepac.org"
                                    class="text-universe hover:bg-wedgewood-50 flex items-center justify-center rounded-md border border-transparent bg-white px-4 py-3 text-base font-medium shadow-xs sm:px-8"
                                >
                                    Nous recruter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employers logo cloud -->
    <div class="bg-gray-100">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-semibold tracking-wide text-gray-500 uppercase">
                Plus de 100 exploitants nous font confiance
            </p>
            <div class="-mx-4 mt-6 flex flex-wrap justify-center">
                @foreach (config('public-site.employers_showcase') as $employer)
                    @php
                        if (File::exists(public_path('media/logos/'.Str::slug($employer).'-logo-wide-gray-400.svg'))) {
                            $src = asset('media/logos/'.Str::slug($employer).'-logo-wide-gray-400.svg');
                        } else {
                            $src = asset('media/logos/'.Str::slug($employer).'-logo-gray-400.svg');
                        }
                    @endphp
                    <div class="flex w-1/3 flex-none justify-center p-4 md:w-1/4 lg:w-1/5">
                        <img class="h-full max-h-12 w-auto" src="{{ $src }}" alt="{{ $employer }}" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-public::layout>
