@use(Laravel\Head\Facades\Head)

@php
    Head::title('En hommage à nos EPL disparus')
        ->description('L’AGEPAC honore la mémoire des Élèves Pilotes de Ligne qui nous ont quittés.')
        ->ogImage(asset('media/737-into-sunset.jpg'), alt: 'Un avion de ligne vu de l’arrière volant vers le coucher de soleil');
@endphp

<x-public::layout>
    <x-slot name="header" class="bg-universe relative" raw>
        <div>
            <div class="absolute inset-x-0 top-0 z-10">
                <div class="relative mx-auto max-w-7xl">
                    <x-public::navbar overlay />
                </div>
            </div>

            <main>
                <div class="bg-universe relative">
                    <div class="absolute inset-x-0 bottom-0 lg:top-0 lg:h-full">
                        <div class="h-full w-full lg:grid lg:grid-cols-2">
                            <div class="h-full lg:relative lg:col-start-2">
                                <img
                                    class="h-full w-full object-cover opacity-50 lg:absolute lg:inset-0"
                                    src="{{ asset('media/737-into-sunset.jpg') }}"
                                    alt="An airline jet seen from behind flying into the sunset"
                                />
                                <div
                                    aria-hidden="true"
                                    class="from-universe absolute inset-x-0 top-0 h-32 bg-linear-to-b lg:inset-y-0 lg:left-0 lg:h-full lg:w-32 lg:bg-linear-to-r xl:w-44"
                                ></div>
                                <div
                                    aria-hidden="true"
                                    class="from-universe absolute inset-x-0 bottom-0 h-12 bg-linear-to-t"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div class="mx-auto max-w-4xl px-4 pt-20 sm:px-6 lg:grid lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-2 lg:gap-x-8 lg:px-8 lg:pt-24">
                        <div class="relative pt-12 pb-64 md:pb-120 lg:col-start-1 lg:pb-24">
                            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-white sm:mt-5 sm:text-6xl lg:mt-6 xl:text-6xl">
                                En hommage à nos EPL disparus
                            </h1>

                            <blockquote class="mt-6 sm:mt-8 md:flex md:grow md:flex-col">
                                <div class="relative text-base font-medium text-gray-300 sm:mt-5 sm:text-xl md:grow lg:text-lg xl:text-xl">
                                    <x-public::icon.left-quotes class="absolute top-0 left-0 h-8 w-8 -translate-x-3 -translate-y-2 transform text-gray-700" />
                                    <p class="relative">
                                        Une fois que vous aurez goûté au vol, vous marcherez à jamais les yeux tournés
                                        vers le ciel, car c’est là que vous êtes allés, et c’est là que toujours vous
                                        désirerez ardemment retourner.
                                    </p>
                                </div>
                                <footer class="mt-2 text-base font-medium text-gray-400">— Léonard de Vinci</footer>
                            </blockquote>

                            <ul class="mt-10 space-y-2 text-base text-gray-300">
                                @foreach (config('public-site.fallen_epls') as $fallenEpl)
                                    <li class="flex items-center">
                                        <x-public::icon.dove class="mr-2 h-4 w-4 text-gray-500" aria-hidden="true" />
                                        {{ $fallenEpl['name'] }} – {{ $fallenEpl['promotion'] }} – {{ $fallenEpl['death'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- More main page content here... -->
            </main>
        </div>
    </x-slot>
</x-public::layout>
