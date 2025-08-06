<x-public::layout title="Contact">
    <x-slot
        name="header"
        backdrop="{{ asset('media/innsbruck-flight-line.jpg') }}"
        alt="A couple pilot students walking towards their aircraft on the Innsbruck apron"
    >
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            Contactez-nous
        </h1>
        <p class="mt-6 text-xl text-cyan-100 max-w-3xl">
            L’AGEPAC est l’association représentative des Élèves Pilotes de Ligne de l’ENAC. Vous avez des questions sur
            nos actions ? Une folle envie de lier un partenariat ? Vous trouverez les meilleurs canaux pour nous joindre
            ci-dessous.
        </p>
    </x-slot>

    <!-- Side-by-side grid -->
    <div class="bg-white">
        <div id="contact" class="max-w-md mx-auto py-24 px-4 sm:max-w-3xl sm:py-32 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="divide-y divide-gray-200 space-y-16">
                <section class="pb-16 lg:grid lg:grid-cols-3 lg:gap-8" aria-labelledby="contact-heading">
                    <h2 id="contact-heading" class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        Contactez-nous
                    </h2>
                    <div class="mt-8 grid grid-cols-1 gap-12 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-12 lg:mt-0 lg:col-span-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Emploi & réseau</h3>
                            <dl class="mt-2 text-base text-gray-600">
                                <div>
                                    <dt class="sr-only">Email</dt>
                                    <dd>partners@agepac.org</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Presse & Médias</h3>
                            <dl class="mt-2 text-base text-gray-600">
                                <div>
                                    <dt class="sr-only">Email</dt>
                                    <dd>media@agepac.org</dd>
                                </div>
                                <div class="mt-1">
                                    <dt class="sr-only">Webpage</dt>
                                    <dd>
                                        <a class="text-wedgewood-500 font-medium hover:underline" href="/press">
                                            En savoir plus <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Contacter le Bureau</h3>
                            <dl class="mt-2 text-base text-gray-600">
                                <div>
                                    <dt class="sr-only">Email</dt>
                                    <dd>bureau@agepac.org</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Dire bonjour</h3>
                            <dl class="mt-2 text-base text-gray-600">
                                <div>
                                    <dt class="sr-only">Email</dt>
                                    <dd>bonjour@agepac.org</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </section>
                <section class="lg:grid lg:grid-cols-3 lg:gap-8" aria-labelledby="location-heading">
                    <h2 id="location-heading" class="text-2xl font-extrabold text-gray-900 sm:text-3xl">Correspondance</h2>
                    <div class="mt-8 grid grid-cols-1 gap-12 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-12 lg:mt-0 lg:col-span-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Toulouse</h3>
                            <div class="mt-2 text-base text-gray-600 space-y-1">
                                <p>AGEPAC</p>
                                <p>7 avenue Edouard Belin</p>
                                <p>BP 54005</p>
                                <p>31055 Toulouse Cedex 4</p>
                                <p>France</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-public::layout>
