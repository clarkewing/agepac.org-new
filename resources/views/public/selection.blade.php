<x-public::layout title="La sélection EPL">
    <x-slot
        name="header"
        class="bg-white"
    >
        <div class="pt-6 pb-16 sm:pb-24 lg:pb-32">
            <main class="mt-8 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 lg:mt-18">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                    <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left lg:flex lg:flex-col lg:justify-center">
                        <h1 class="block text-4xl tracking-tight font-extrabold sm:text-5xl xl:text-6xl">
                            <span class="text-universe">Alors comme ça tu veux</span>
                            <span class="text-vermilion-400">devenir EPL</span>
                            <span class="text-universe">?</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                            L’ENAC propose 4 voies d'accès au cursus EPL. Toutes ont en commun un concours très sélectif.
                        </p>

                        <div class="mt-6 sm:max-w-lg sm:mx-auto lg:hidden">
                            <div class="mx-auto w-full rounded-lg shadow-lg">
                                <div class="block w-full bg-white rounded-lg overflow-hidden">
                                    <img
                                        class="w-full"
                                        src="{{ asset('media/flight-group-tb20.jpg') }}"
                                        alt="An EPL flight group posing next to their TB20 seen from the front"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                            <p class="text-base font-medium text-gray-900">
                                Découvre le Guide EPL, régulièrement mis à jour.
                            </p>
                            <a
                                class="mt-3 flex w-full sm:shrink-0 sm:inline-flex sm:w-auto items-center justify-center px-6 py-3 border border-transparent shadow-xs text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-vermilion-400"
                                href="{{ asset('media/guide-epl-2023.pdf') }}"
                                target="_blank"
                                download
                            >
                                <x-heroicon-o-arrow-down-tray class="-ml-1 mr-3 h-5 w-5" />
                                Télécharger le Guide EPL
                            </a>
{{--                            <p class="mt-3 text-sm text-gray-500">--}}
{{--                                Si tu trouves le Guide EPL utile, n'hésite pas à--}}
{{--                                <a href="#" class="font-medium text-gray-900 underline">faire un don</a>.--}}
{{--                                Les dons permettent à l'AGEPAC d'exister.--}}
{{--                            </p>--}}
                        </div>
                    </div>

                    <div class="hidden lg:col-span-6 lg:flex lg:items-center">
                        <div class="mx-auto w-full rounded-lg shadow-lg">
                            <div class="block w-full bg-white rounded-lg overflow-hidden">
                                <img
                                    class="w-full"
                                    src="{{ asset('media/flight-group-tb20.jpg') }}"
                                    alt="An EPL flight group posing next to their TB20 seen from the front"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 overflow-hidden">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
           <x-public::pattern.dots class="hidden lg:block absolute left-full transform -translate-x-1/2 -translate-y-1/4" width="404" height="784" />

            <x-public::feature-header>
                <x-slot name="title">Le Concours EPL</x-slot>
                <x-slot name="description">
                    La formation EPL dispensée à l’ENAC comporte trois voies d’accès "historiques" et une quatrième,
                    plus récente, décrite plus bas.
                </x-slot>
            </x-public::feature-header>

            <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <x-public::feature-section-header>
                        <x-slot name="title">Les filières</x-slot>
                    </x-public::feature-section-header>

                    <dl class="mt-10 space-y-10">
                        <x-public::feature-section-item>
                            <x-slot name="icon" class="bg-wedgewood-500">S</x-slot>
                            <x-slot name="title">Cursus EPL/S (Scientifique)</x-slot>
                            <x-slot name="description">
                                Le cursus EPL/S (Scientifique) est ouvert aux jeunes ayant un bagage scientifique mais
                                pas nécessairement un bagage aéronautique. Appelés "ab initio" , ils se formeront de A à
                                Z à l’ENAC jusqu'à l’obtention de leur "Frozen ATPL". Concrètement, ils auront en main
                                une fois diplômés : la partie théorique de la licence de pilote de ligne (ATPL) validée,
                                une licence de pilote commercial (CPL), une qualification de vol aux instruments sur
                                avions multimoteurs (IRME) et un certificat de formation au travail en équipage (MCC).
                                Ils auront également réussi l’examen spécifique de la langue anglaise de l’appendice 1
                                du FCL.055D.
                            </x-slot>
                        </x-public::feature-section-item>

                        <x-public::feature-section-item>
                            <x-slot name="icon" class="bg-dandelion-300">U</x-slot>
                            <x-slot name="title">Cursus EPL/U (Universitaire)</x-slot>
                            <!-- TODO: Remove badge in March 2023 -->
                            @if(today()->isBefore('2023-03-01'))
                                <x-slot name="badge" class="bg-shiraz-600 text-white">
                                    Nouveau
                                    <span class="absolute -top-36" id="new-epl-u-criteria"></span>
                                </x-slot>
                            @endif
                            <x-slot name="description">
                                Le cursus EPL/U (Universitaire) s’adresse à des candidats ayant débuté un cursus
                                universitaire depuis au moins 2 ans et détenteurs a minima d’une licence
                                EASA LAPL (Light Aircraft Pilot License) sur avion, hélicoptère ou planeur
                                — <span class="italic">la détention de l’ATPL(A) théorique n’étant plus obligatoire depuis 2022</span>.
                                Les candidats devront avoir déjà obtenu leur FCL.055D. Ils rejoindront les EPL/S en
                                formation théorique — <span class="italic">même s’ils sont déjà détenteurs de l’ATPL(A) théorique</span>.
                            </x-slot>
                        </x-public::feature-section-item>

                        <x-public::feature-section-item>
                            <x-slot name="icon" class="bg-vermilion-400">P</x-slot>
                            <x-slot name="title">Cursus EPL/P (Pratique)</x-slot>
                            <x-slot name="description">
                                Le cursus EPL/P (Pratique) concerne des candidats à un stade déjà bien entamé de leur
                                formation ; ils doivent être en possession de leur ATPL théorique, du FCL.055D, ainsi
                                que de leur licence de pilote commercial (CPL). Ils rejoindront la formation ENAC pour
                                la phase de vol aux instruments sur avion multimoteur (IRME) puis termineront par la
                                formation au travail en équipage (MCC).
                            </x-slot>
                        </x-public::feature-section-item>
                    </dl>

                    <x-public::alert class="mt-6" type="info">
                        Retrouve l’ensemble des critères d'éligibilité sur le
                        <a
                            class="inline-flex items-center font-medium text-blue-700 underline hover:text-blue-600 hover:no-underline"
                            href="https://www.enac.fr/fr/epl-eleve-pilote-de-ligne"
                            target="_blank"
                        >
                            <span>site officiel de l’ENAC</span>
                            <x-heroicon-s-arrow-top-right-on-square class="ml-1 w-4 h-4" />
                        </a>
                    </x-public::alert>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
                   <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />
                    <img
                        class="relative mx-auto lg:rounded-lg lg:shadow-xl"
                        width="490"
                        src="{{ asset('media/mpl-overhead.jpg') }}"
                        alt="Montpellier airport and its runway seen from above alongside the wing of a TB20"
                    />
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="784" />

            <div class="relative mt-12 sm:mt-16 lg:mt-24">
                <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="lg:col-start-2">
                        <x-public::feature-section-header>
                            <x-slot name="title">Les étapes du concours</x-slot>
                            <x-slot name="description">
                                Tant redouté par beaucoup, le concours d'entrée est extrêmement sélectif. Il se compose
                                comme suit :
                            </x-slot>
                        </x-public::feature-section-header>

                        <dl class="mt-10 space-y-10">
                            <x-public::feature-section-item>
                                <x-slot name="icon">1</x-slot>
                                <x-slot name="title">Les Écrits</x-slot>
                                <x-slot name="badge">EPL/S</x-slot>
                                <x-slot name="description">
                                    Seulement pour le cursus EPL/S, la première étape du concours consiste en 3 épreuves
                                    écrites: une épreuve de Mathématiques, une épreuve de Physique et une épreuve
                                    d’anglais. Le niveau des ces épreuves est celui d’une première année de classe
                                    préparatoire aux grandes écoles.
                                </x-slot>
                            </x-public::feature-section-item>

                            <x-public::feature-section-item>
                                <x-slot name="icon">2</x-slot>
                                <x-slot name="title">Les PSY 1</x-slot>
                                <x-slot name="badge" style="background: linear-gradient(105deg, #B0D3E1 0%, #B0D3E1 58.1%, #F8E483 0%, #F8E483 73%, #FE9A85 0%, #FE9A85 100%);">EPL/S/U/P</x-slot>
                                <x-slot name="description">
                                    Constitués de tests psychotechniques et psychomoteurs, ils permettent d’évaluer des
                                    aptitudes mentales nécessaires au métier de pilote de ligne telles que l’attention
                                    divisée, le raisonnement logique, le repérage dans l’espace ou encore le calcul
                                    mental.
                                </x-slot>
                            </x-public::feature-section-item>

                            <x-public::feature-section-item>
                                <x-slot name="icon">3</x-slot>
                                <x-slot name="title">Les PSY 2</x-slot>
                                <x-slot name="badge" style="background: linear-gradient(105deg, #B0D3E1 0%, #B0D3E1 58.1%, #F8E483 0%, #F8E483 73%, #FE9A85 0%, #FE9A85 100%)">EPL/S/U/P</x-slot>
                                <x-slot name="description">
                                    Enfin la troisième étape est cruciale car elle permet d’évaluer la capacité au
                                    travail en équipage des candidats et leur motivation. Pour ce faire, les candidats
                                    vont être mis en situation par le biais d’épreuves de groupe; des débats et des
                                    énigmes. Ensuite les candidats passeront des entretiens individuels : un premier
                                    mené par des pilotes et un second mené par un psychologue. Pour finir, la sélection
                                    se clôture par un oral d’anglais. Dans le cadre des filières U et P c’est la note
                                    obtenue à l’oral d’anglais qui permettra de classer les candidats. Pour la filière
                                    S, les notes des écrits rentreront également en compte.
                                </x-slot>
                            </x-public::feature-section-item>
                        </dl>
                    </div>

                    <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />
                        <img
                            class="relative mx-auto lg:rounded-lg lg:shadow-xl"
                            width="490"
                            src="{{ asset('media/st-yan-night-approach.jpg') }}"
                            alt="The Saint-Yan approach lights seen from the cockpit of a BE58 at night"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 px-8">
        <div class="relative max-w-2xl lg:max-w-3xl mx-auto">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-gray-50 px-2 text-gray-500">
                    <x-heroicon-o-paper-airplane class="h-5 w-5 text-gray-500" aria-hidden="true" />
                </span>
            </div>
        </div>
    </div>


    <div class="py-12 bg-gray-50 overflow-hidden">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
           <x-public::pattern.dots class="hidden lg:block absolute left-full transform -translate-x-1/2 -translate-y-1/4" width="404" height="784" />

            <x-public::feature-header>
                <x-slot name="title">Le Cycle Préparatoire à l'ATPL</x-slot>
            </x-public::feature-header>

            <div class="relative mt-6 lg:mt-12 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <div class="mt-10 text-base text-gray-500 space-y-10">
                        <p>
                            Depuis 2011, il existe une quatrième voie d'accès à la formation de pilote de ligne de
                            l’ENAC : le cycle préparatoire à l’ATPL. Envisagée comme une année de prépa intégrée,
                            l’année de cycle préparatoire à l’ATPL permet aux jeunes sélectionnés ayant depuis peu le
                            BAC en poche de se mettre à niveau en Mathématiques, Physique, Anglais et Français. Ils
                            aborderont également des sujets purement aéronautiques, le tout leur permettant de rejoindre
                            sereinement le cursus EPL/S à son commencement.
                        </p>
                        <p>
                            Les critères d'admissibilité nécessitent que le candidat ait entre 16 et 21 ans, qu’il
                            bénéficie d’une bourse de scolarité et qu’il soit titulaire d’un Brevet d’Initiation
                            Aéronautique (BIA), ou d’un autre titre aéronautique, ou bien encore qu’il soit présenté par
                            une fédération aéronautique ou un lycée aéronautique.
                        </p>
                    </div>

                    <x-public::alert class="mt-6" type="info">
                        Retrouve l’ensemble des informations concernant le cursus préparatoire à l'ATPL sur le
                        <a
                            class="inline-flex items-center font-medium text-blue-700 underline hover:text-blue-600 hover:no-underline"
                            href="https://www.enac.fr/fr/cycle-preparatoire-atpl"
                            target="_blank"
                        >
                            <span>site officiel de l’ENAC</span>
                            <x-heroicon-s-arrow-top-right-on-square class="ml-1 w-4 h-4" />
                        </a>
                    </x-public::alert>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
                   <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />
                    <img
                        class="relative mx-auto lg:rounded-lg lg:shadow-xl"
                        width="490"
                        src="{{ asset('media/pilot-tb20.jpg') }}"
                        alt="An EPL student wearing sunglasses inside a TB20 cockpit seen from the rear"
                    />
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="784" />

            <div class="relative mt-12 sm:mt-16 lg:mt-24">
                <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="lg:col-start-2">
                        <x-public::feature-section-header>
                            <x-slot name="title">Les étapes du concours</x-slot>
                        </x-public::feature-section-header>

                        <dl class="mt-10 space-y-10">
                            <x-public::feature-section-item>
                                <x-slot name="icon">1</x-slot>
                                <x-slot name="title">Le Dossier</x-slot>
                                <x-slot name="description">
                                    Il comporte le dossier scolaire du candidat mais aussi un dossier de candidature. Il
                                    leur est demandé une lettre de motivation et une ou deux lettres de recommandation
                                    par des instructeurs aéronautiques. Les candidats devront joindre leur titre
                                    aéronautique pour compléter ce dossier.
                                </x-slot>
                            </x-public::feature-section-item>

                            <x-public::feature-section-item>
                                <x-slot name="icon">2</x-slot>
                                <x-slot name="title">Les Écrits</x-slot>
                                <x-slot name="description">
                                    2/3 de mathématiques et 1/3 de physique pour composer l'évaluation scientifique des
                                    candidats. Les questions sont basées sur le programme de la classe de terminale
                                    STI2D (Sciences et Technologies de l’Industrie et du développement Durable) de
                                    l’année de la sélection. La seconde partie des écrits est l’évaluation aéronautique.
                                    Elle est basée sur le programme en vigueur du Brevet d’Initiation Aéronautique (BIA)
                                    de l’année de la sélection.
                                </x-slot>
                            </x-public::feature-section-item>

                            <x-public::feature-section-item>
                                <x-slot name="icon">3</x-slot>
                                <x-slot name="title">Les PSY 1</x-slot>
                                <x-slot name="description">
                                    Constitués de tests psychotechniques et psychomoteurs, ils permettent d’évaluer des
                                    aptitudes mentales nécessaires au métier de pilote de ligne telles que l’attention
                                    divisée, le raisonnement logique, le repérage dans l’espace ou encore le calcul
                                    mental.
                                </x-slot>
                            </x-public::feature-section-item>

                            <x-public::feature-section-item>
                                <x-slot name="icon">4</x-slot>
                                <x-slot name="title">Les PSY 2</x-slot>
                                <x-slot name="description">
                                    Enfin la quatrième étape est cruciale car elle permet d’évaluer la capacité au
                                    travail en équipage des candidats et leur motivation. Pour ce faire, les candidats
                                    vont devoir passer des épreuves de groupe. Ensuite les candidats passeront des
                                    entretiens individuels devant un jury pour mesurer leur motivation. Pour finir, la
                                    sélection se clôture par un oral d’anglais.
                                </x-slot>
                            </x-public::feature-section-item>
                        </dl>
                    </div>

                    <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />
                        <img
                            class="relative mx-auto lg:rounded-lg lg:shadow-xl"
                            width="490"
                            src="{{ asset('media/altocumulus-over-be58-wing.jpg') }}"
                            alt="Rows of altocumulus clouds seen above the wing of a BE58 aircraft banking into a turn over the countryside"
                        />

                        <div class="px-4">
                            <x-public::alert class="relative mt-12 mx-auto max-w-[490px] shadow-xl" type="warning">
                                Chaque étape de sélection est éliminatoire. A l’issue des quatre étapes, les candidats
                                seront classés en fonction des notes attribuées ou obtenues au dossier de candidature, à
                                l’entretien de motivation, à l’oral d’anglais et à l'évaluation scientifique et
                                aéronautique. Il y a 5 places ouvertes par an.
                            </x-public::alert>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ -->
{{--    <div class="bg-white">--}}
{{--        <div id="faq" class="max-w-md mx-auto py-24 px-4 sm:max-w-3xl sm:py-32 sm:px-6 lg:max-w-7xl lg:px-8">--}}
{{--            <div class="lg:grid lg:grid-cols-3 lg:gap-8">--}}
{{--                <div>--}}
{{--                    <h2 class="text-3xl font-extrabold text-universe">Foire aux questions</h2>--}}
{{--                    <p class="mt-4 text-lg text-gray-500">--}}
{{--                        Vous ne trouvez pas la réponse que vous cherchez ? Contactez notre--}}
{{--                        <a href="{{ route('public.contact') }}" class="font-medium text-vermilion-500 hover:text-vermilion-600">équipe</a>.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div class="mt-12 lg:mt-0 lg:col-span-2">--}}
{{--                    <dl class="space-y-12">--}}
{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Quelles sont les differences entre EPL/S/U/P ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Les formations sont différenciées par leurs prérequis au concours d’entrée. Plus de--}}
{{--                                détails sont disponibles sur notre page--}}
{{--                                <a href="{{ route('public.epl.selection') }}">Le Concours</a>.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Combien coute l'entrée à l’ENAC ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                La formation est payée par l'État (seule formation de Pilote de Ligne gratuite avec les--}}
{{--                                Cadets Air France).<br>--}}
{{--                                Reste à votre charge le logement (compter 250€ par mois sans les aides à l’ENAC Toulouse--}}
{{--                                ainsi que dans les divers centres) et les frais de restauration (compter 10€/jour au--}}
{{--                                moins) comme tout étudiant.<br>--}}
{{--                                En sus les non-boursiers doivent payer les frais d’inscription au concours qui sont--}}
{{--                                d’environ 100€.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Quel niveau d’anglais faut-il ? / Faut-il être billingue pour réussir le concours ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                L’anglais devient de plus en plus crucial pour cette formation, le niveau est rehaussé--}}
{{--                                partout et il devient critique de mettre toutes les chances de son côté.<br>--}}
{{--                                Sans avoir l’obligation d’être parfaitement billingue en anglais, une très bonne aisance--}}
{{--                                est indispensable. Cela passe par de bonnes connaissances grammaticales, un vocabulaire--}}
{{--                                riche pas uniquement orienté aéronautique et une bonne expression orale.<br>--}}
{{--                                La note éliminatoire au concours est de 8/20 en anglais alors qu’elle est de 5/20 dans--}}
{{--                                les autres matières. L’oral du concours représente également une difficultée--}}
{{--                                supplémentaire.<br>--}}
{{--                                Il est possible d’améliorer franchement son niveau en séjournant à l’etranger. Un séjour--}}
{{--                                long (au moins un mois, jusqu’un an) est très profitable et est souvent bien vu de la--}}
{{--                                part des recruteurs.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Y a-t-il beaucoup de filles EPL à l’ENAC ? Sont elles traîtées de la même manière ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                En règle générale le pourcentage de filles se présentant au concours EPLs est--}}
{{--                                sensiblement égal au pourcentage de filles reçues.<br>--}}
{{--                                Il est de l'ordre de 10 %. Pour une promotion de 25 stagiaires cela représente environ 3--}}
{{--                                filles.<br>--}}
{{--                                Il est néanmoins plus que vraisemblable qu’un jour tu sois confrontée à un instructeur--}}
{{--                                ou un collègue bien macho (car il y en a c'est certain), ce jour là tu devras peut être--}}
{{--                                plus faire tes preuves mais tu auras aussi l’effet inverse genre l'instructeur bien--}}
{{--                                affreux avec ton (tes) binome(s), et tout paternel et sympa avec toi.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Je suis en 5ème/4ème...Terminale et je voudrais savoir combien de moyenne il faut avoir--}}
{{--                                en maths, physique, anglais pour avoir une chance de rentrer à l'ENAC.--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Il n’y a pas de moyenne type pour avoir une chance. Le mieux que tu puisses faire est de--}}
{{--                                travailler sérieusement, de passer un BAC S (le minimum pour présenter le concours--}}
{{--                                EPL/S) et d’entrer en Maths Sup si tu veux mettre le plus de chances de ton côté.<br>--}}
{{--                                Personne ne donnera jamais de notes types car c’est trop subjectif. Il faut travailler--}}
{{--                                sérieusement, ne pas baisser les bras et croire en ses chances.<br>--}}
{{--                                Donc travaille bien et tu pourras espérer un jour faire d’un cockpit ton bureau. Le--}}
{{--                                concours est à la portée de tout élève qui le prépare vraiment (à fond, à fond...) et--}}
{{--                                qui bosse bien l’anglais.<br>--}}
{{--                                Mais il ne faut pas oublier en cas d’echec que voler est aussi et surtout un plaisir !--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Faut-il être francais pour entrer à l’ENAC en tant qu’EPL ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Le concours EPL est ouvert à tous les ressortissants de l’Union Européenne.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Faut il avoir fait une prépa pour intégrer l’ENAC ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Non. Le programme du concours est basé sur le programme de Maths Sup. Il est--}}
{{--                                disponible--}}
{{--                                <a href="https://prepas.org/index.php?rubrique=53">ici</a>.<br>--}}
{{--                                S’il est clair que la voie royale est de passer par la prépa, la seule condition pour--}}
{{--                                acceder au concours et de posseder un BAC S. Après le concours peut être préparé à la--}}
{{--                                FAC, en école de commerce, d'ingénieurs, en IUT etc... La seule limite étant vous même,--}}
{{--                                vos capacités à comprendre, apprendre, etc...--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Quelle est la meilleure prépa pour intégrer l’ENAC ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Soyons clairs et sans équivoque : il n’y a pas de prépa miracle. Toutes les classes--}}
{{--                                préparatoires se valent pour intégrer l’ENAC en tant qu’EPL. Le tout est de bien bosser--}}
{{--                                et de se sentir à l’aise dans la prépa que l’on a choisi. Rien ne sert de viser une--}}
{{--                                prépa réputée si c’est pour se retrouver à la traîne dans ladite prépa !<br>--}}
{{--                                Il peut être utile de récuperer les annales sur le site de l’ENAC et de demander à vos--}}
{{--                                professeurs de vous les faire travailler.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Serais-je affecté dans une compagnie aérienne à la fin de ma formation ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Le concours EPL est ni un concours de recrutement de fonctionnaires, ni un concours--}}
{{--                                organisé pour le compte de compagnies aériennes. Le concours et la formation sont--}}
{{--                                organisés et financés par la DGAC afin de permettre l’accès à ce métier à des jeunes--}}
{{--                                quels que soient leurs moyens matériels. À l’issue de la formation, vous serez donc--}}
{{--                                disponible sur le marché de l’emploi, au même titre que si vous étiez diplômé d’une--}}
{{--                                université ou d’une école d’ingénieur.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Je n’ai jamais piloté ; serais-je capable de suivre la formation ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Le programme de formation EPL/S est conçu pour des élèves n’ayant jamais piloté. Il est--}}
{{--                                donc accessible à tous les lauréats du concours, même s’ils n’ont aucune expérience--}}
{{--                                antérieure.<br>--}}
{{--                                La pratique nous montre que certains élèves, arrivant en formation avec plusieurs--}}
{{--                                dizaines d’heures de vol et qui, par conséquent, ont pris des habitudes de pilotage--}}
{{--                                différentes de la méthode utilisée par l’ENAC, ont parfois plus de difficultés que leurs--}}
{{--                                collègues sans expérience et donc plus réceptifs à la méthodologie enseignée.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}

{{--                        <x-public::faq-item>--}}
{{--                            <x-slot name="question">--}}
{{--                                Est-ce que je passerai ma licence de pilote privé (PPL) pendant ma formation ?--}}
{{--                            </x-slot>--}}
{{--                            <x-slot name="answer">--}}
{{--                                Non, car le cursus EPL est une formation intégrée, c'est-à-dire que tout le programme--}}
{{--                                est orienté vers le seul objectif final de faire de vous un professionnel apte à exercer--}}
{{--                                comme Officier Pilote de Ligne (OPL) sur avion de transport public. Même si votre niveau--}}
{{--                                technique permet de présenter le test PPL en cours de formation, le programme qui vous--}}
{{--                                est dispensé n’est pas approuvé pour cela.--}}
{{--                            </x-slot>--}}
{{--                        </x-public::faq-item>--}}
{{--                    </dl>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

</x-public::layout>
