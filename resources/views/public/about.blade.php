<x-public::layout title="Qui sommes-nous ?" class="bg-white">
    <x-slot
        name="header"
        backdrop="{{ asset('media/flight-group-be58.jpg') }}"
        alt="Four EPLs posing in front of a Beechcraft Baron 58 aircraft"
    >
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            Qui sommes-nous ?
        </h1>
    </x-slot>

    <!-- Mission statement section -->
    <div class="relative overflow-hidden">
        <div class="lg:mx-auto lg:max-w-7xl lg:px-8 lg:grid lg:grid-cols-2 lg:gap-24 lg:items-start">
            <div class="relative mx-auto max-w-md px-4 sm:max-w-3xl sm:px-6 lg:order-2 lg:px-0">
                <!-- Content area -->
                <div class="pt-6 lg:pt-20">
                    <h2 class="text-3xl text-gray-900 font-extrabold tracking-tight sm:text-4xl">
                        Notre mission
                    </h2>

                    <div class="mt-6 text-gray-500 space-y-6">
                        <div>
                            <p class="text-lg text-justify">
                                L’Association Générale des Élèves Pilotes de l’Aviation Civile est une association loi 1901
                                qui, depuis 1992, regroupe les Élèves Pilotes de Ligne formés à l’École Nationale de
                                l’Aviation Civile.
                            </p>
                            <p class="mt-2 text-lg text-justify">
                                Sa mission est avant tout de garantir la pérennité de la filière publique de formation au
                                pilotage, mais aussi d’assurer une bonne représentativité des actuels et anciens élèves dans
                                la communauté aéronautique.
                            </p>
                        </div>
                        <div class="text-base leading-7">
                            <p>
                                Les buts poursuivis sont multiples :
                            </p>
                            <ul class="list-disc pl-6 text-justify">
                                <li>Aider, par les moyens dont elle dispose, les EPL à accéder à un poste de navigant.</li>
                                <li>Servir d'interlocuteur privilégié dans différentes discussions et actions.</li>
                                <li>Faire interface entre les EPL sortis de formation, et les autres acteurs du monde du transport aérien.</li>
                                <li>Promouvoir et défendre la filière de formation d'État (dite filière EPL).</li>
                                <li>Proposer des activités, maintenir des contacts entre EPL.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Stats section -->
                <div class="mt-10">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-8">
                        <div class="border-t-2 border-gray-100 pt-6">
                            <dt class="text-base font-medium text-gray-500">Fondée</dt>
                            <dd class="text-3xl font-extrabold tracking-tight text-gray-900">1992</dd>
                        </div>

                        <div class="border-t-2 border-gray-100 pt-6">
                            <dt class="text-base font-medium text-gray-500">Membres actifs</dt>
                            <dd class="text-3xl font-extrabold tracking-tight text-gray-900">300</dd>
                        </div>
                    </dl>
{{--                    <div class="mt-10">--}}
{{--                        <a href="#" class="text-base font-medium text-shiraz-600 hover:underline">--}}
{{--                            Découvrez nos actions <span aria-hidden="true">&rarr;</span>--}}
{{--                        </a>--}}
{{--                    </div>--}}
                </div>
            </div>

            <div class="relative sm:py-16 lg:order-1 lg:py-0 mt-12 sm:mt-8">
                <div aria-hidden="true" class="hidden sm:block lg:absolute lg:inset-y-0 lg:right-0 lg:w-screen">
                    <div class="absolute inset-y-0 right-1/2 w-full bg-gray-50 rounded-r-3xl lg:right-72"></div>
                   <x-public::pattern.dots class="absolute top-8 left-1/2 -ml-3 lg:-right-8 lg:left-auto lg:top-12" width="404" height="392" />
                </div>
                <div class="relative mx-auto max-w-md px-4 sm:max-w-3xl sm:px-6 lg:px-0 lg:max-w-none lg:py-20">
                    <!-- Testimonial card-->
                   <x-public::testimonial
                        author="Hugo Clarke-Wing, Ancien Président 2020&#8209;2023"
                        photo="{{ asset('media/leadership/hugo-clarke-wing-wide.jpg') }}"
                        alt="Hugo Clarke-Wing, in uniform, smiling at the camera"
                    >
                        L’AGEPAC — au-delà d’être une force de proposition aux côtés de nos divers partenaires — c’est
                        une association de pilotes qui partagent les mêmes valeurs d’excellence et une passion pour
                        l’aéronautique sous toute ses formes.
                    </x-public::testimonial>
                </div>
            </div>
        </div>
    </div>

    <!-- Values section -->
    <div class="relative mx-auto max-w-md py-12 px-4 sm:max-w-3xl sm:px-6 lg:px-8 lg:max-w-7xl mt-8 lg:mt-16">
       <x-public::pattern.dots class="hidden lg:block absolute top-0 left-full transform -translate-x-1/2 -translate-y-3/4 lg:left-auto lg:right-full lg:translate-x-2/3 lg:translate-y-1/3" width="404" height="468" />

        <div class="relative lg:grid lg:grid-cols-3 lg:gap-x-8">
            <div class="lg:col-span-1">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Guidés par nos valeurs
                </h2>
            </div>
            <dl class="mt-10 space-y-10 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-x-8 sm:gap-y-10 lg:mt-0 lg:col-span-2">
                <x-public::dl-item title="Passion">
                    <x-slot name="icon">
                        <x-heroicon-s-sparkles class="h-6 w-6" aria-hidden="true"/>
                    </x-slot>

                    La passion pour l’aviation sous toutes ses formes nous suit tout au long de nos vies et nous pousse
                    sans cesse à nous investir.
                </x-public::dl-item>

                <x-public::dl-item title="Compagnonnage">
                    <x-slot name="icon">
                        <x-heroicon-s-user-group class="h-6 w-6" aria-hidden="true" />
                    </x-slot>

                    Avec l’entraide et la cohésion au coeur de notre ADN, l’AGEPAC ne manque jamais une occasion de
                    rassembler les générations autour de moments chaleureux et festifs.
                </x-public::dl-item>

                <x-public::dl-item title="Résilience">
                    <x-slot name="icon">
                        <x-heroicon-s-fire class="h-6 w-6" aria-hidden="true" />
                    </x-slot>

                    Le chemin vers un cockpit n’étant pas tout tracé, la capacité d’adaptation de nos membres leur
                    permet de découvrir de nouveaux horizons et d’appliquer leur savoir faire à diverses missions.
                </x-public::dl-item>

                <x-public::dl-item title="Rayonnement">
                    <x-slot name="icon">
                        <x-heroicon-s-globe-europe-africa class="h-6 w-6" aria-hidden="true" />
                    </x-slot>

                    Portés par un esprit d’ouverture fort, nous avons à cœur d’exporter notre passion et nos compétences
                    au service des différents acteurs de l’aérien, tout en portant haut les couleurs de l’ENAC.
                </x-public::dl-item>

                <x-public::dl-item title="Partage">
                    <x-slot name="icon">
                        <x-public::icon.hands-helping class="h-6 w-6" aria-hidden="true" />
                    </x-slot>

                    Forte de plus de 30 ans d’existence, notre association est un formidable outil de transmission
                    d’expériences et d’héritage, que ce soit au sein de nos membres, ou en collaboration avec nos
                    partenaires.
                </x-public::dl-item>
            </dl>
        </div>
    </div>

    <!-- Partners logo cloud section -->
    <div class="mt-16 md:mt-24">
        <div class="mx-auto max-w-md px-4 sm:max-w-3xl sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="lg:grid lg:grid-cols-2 lg:gap-24 lg:items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
                        Engagés aux côtés de partenaires de poids
                    </h2>
                    <p class="mt-6 max-w-3xl text-lg leading-7 text-gray-500">
                        Forts d’un réseau construit au long de nos plus de 30 ans d’existence, nous travaillons main
                        dans la main avec des acteurs dessinant l’aviation dans la durée.
                    </p>
                </div>
                <div class="mt-12 grid grid-cols-2 gap-0.5 md:grid-cols-3 lg:mt-0 lg:grid-cols-2">
                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/enac-logo.svg') }}"
                            alt="ENAC"
                        />
                    </div>

                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/snpl-logo.svg') }}"
                            alt="SNPL"
                        />
                    </div>

                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/air-france-logo.svg') }}"
                            alt="Air France"
                        />
                    </div>

                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/ffa-logo.svg') }}"
                            alt="Fédération Française Aéronautique"
                        />
                    </div>

                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/appn-logo.png') }}"
                            alt="Mirage"
                        />
                    </div>

                    <div class="col-span-1 flex justify-center py-7 px-8 bg-gray-50">
                        <img
                            class="max-h-14"
                            src="{{ asset('media/logos/avico-logo-with-plane.svg') }}"
                            alt="Avico"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA section -->
    <div class="relative overflow-hidden mt-24 sm:mt-32 pb-8 sm:py-16">
        <div aria-hidden="true" class="hidden sm:block">
            <div class="absolute inset-y-0 left-0 w-1/2 bg-gray-50 rounded-r-3xl"></div>
           <x-public::pattern.dots class="absolute top-8 left-1/2 -ml-3" width="404" height="392" />
        </div>
        <a
            class="group block mx-auto max-w-md px-4 sm:max-w-3xl sm:px-6 lg:max-w-7xl lg:px-8"
            href="{{ route('public.association.team') }}"
        >
            <div class="relative rounded-2xl px-6 py-10 bg-dandelion-500 group-hover:bg-dandelion-400 overflow-hidden shadow-xl sm:px-12 sm:py-20">
                <div aria-hidden="true" class="absolute inset-0 -mt-72 sm:-mt-32 md:mt-0">
                    <svg class="absolute inset-0 h-full w-full" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 1463 360">
                        <path class="text-dandelion-300/40" fill="currentColor" d="M-82.673 72l1761.849 472.086-134.327 501.315-1761.85-472.086z" />
                        <path class="text-dandelion-600/40" fill="currentColor" d="M-217.088 544.086L1544.761 72l134.327 501.316-1761.849 472.086z" />
                    </svg>
                </div>
                <div class="relative">
                    <div class="sm:text-center">
                        <h2 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
                            Rencontrez notre équipe
                        </h2>
                    </div>
                    <div class="mt-12 lg:flex justify-center -space-y-4 md:-space-x-3 md:space-y-0">
                        <div class="sm:flex justify-center -space-y-4 sm:-space-x-3 sm:space-y-0">
                            <div class="flex justify-center -space-x-3">
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/hugo-clarke-wing.jpg') }}"
                                        alt="Hugo Clarke-Wing"
                                    />
                                </div>
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/jeff-mhanna.jpg') }}"
                                        alt="Jeff Mhanna"
                                    />
                                </div>
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/adrian-lucq-bibiloni.jpg') }}"
                                        alt="Adrián Lucq-Bibiloni"
                                    />
                                </div>
                            </div>
                            <div class="flex justify-center -space-x-3">
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/slim-khouadja.jpg') }}"
                                        alt="Slim Khouadja"
                                    />
                                </div>
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/marek-madl.jpg') }}"
                                        alt="Marek Mádl"
                                    />
                                </div>
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/felix-fouache.jpg') }}"
                                        alt="Félix Fouache"
                                    />
                                </div>
                                <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                    <img
                                        class="h-full w-full object-cover"
                                        src="{{ asset('media/leadership/julien-thomasson.jpg') }}"
                                        alt="Julien Thomasson"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center -space-x-3">
                            <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                <img
                                    class="h-full w-full object-cover"
                                    src="{{ asset('media/leadership/tom-guedj.jpg') }}"
                                    alt="Tom Guedj"
                                />
                            </div>
                            <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                <img
                                    class="h-full w-full object-cover"
                                    src="{{ asset('media/leadership/paul-viviant.jpg') }}"
                                    alt="Paul Viviant"
                                />
                            </div>
                            <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                <img
                                    class="h-full w-full object-cover"
                                    src="{{ asset('media/leadership/theophile-pellissier.jpg') }}"
                                    alt="Théophile Pellissier"
                                />
                            </div>
                            <div class="relative flex-none inline-block h-16 w-16 md:h-20 md:w-20 rounded-full overflow-hidden ring-4 ring-dandelion-500">
                                <img
                                    class="h-full w-full object-cover"
                                    src="{{ asset('media/leadership/morgane-maillet.jpg') }}"
                                    alt="Morgane Maillet"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

</x-public::layout>
