<x-public::layout title="La formation EPL">
    <x-slot
        name="header"
        class="bg-white"
    >
        <div class="pt-6 pb-16 sm:pb-24 lg:pb-32">
            <main class="mt-8 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 lg:mt-18">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                    <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left lg:flex lg:flex-col lg:justify-center">
                        <h1 class="block text-4xl tracking-tight font-extrabold sm:text-5xl xl:text-6xl">
                            <span class="text-universe">La formation</span>
                            <span class="text-vermilion-400">EPL</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-xl lg:text-lg xl:text-xl">
                            La formation d’un EPL se déroule en 5 phases débutant par la préparation de l’ATPL théorique
                            et finissant par l’apprentissage du travail en équipage.
                        </p>
                    </div>
                    <div class="mt-12 sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                        <div class="mx-auto w-full rounded-lg shadow-lg">
                            <div class="block w-full bg-white rounded-lg overflow-hidden">
                                <img
                                    class="w-full"
                                    src="{{ asset('media/students-on-tb20-wing.jpg') }}"
                                    alt="A couple EPL students preparing their flight bags on the wing of a TB20"
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

            <div class="relative lg:mt-12 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <x-public::feature-section-header>
                        <x-slot name="title">L’ATPL théorique</x-slot>
                    </x-public::feature-section-header>

                    <div class="mt-8 text-base text-justify text-gray-600 space-y-2">
                        <p>
                            Après les joies de la sélection, et les premières sensations de vol du stage planeur organisé
                            par l’AGEPAC avant de débuter la formation, il est temps de retourner une dernière fois sur les
                            chaises d’étudiant. Avant de monter dans un avion de l’ENAC, il faut acquérir toutes les
                            connaissances nécessaires au pilote, et plus encore. Apprendre la navigation, le droit aérien,
                            la météo, le fonctionnement d’un moteur etc, c’est l’objectif de l’ATPL théorique que nous
                            passons en 10 mois à Toulouse.
                        </p>
                        <p>
                            Avec l’aide d’une vingtaine de professeurs tous spécialistes de leur domaine, du pilote de
                            chasse au chef de tour de contrôle parisien en passant par les pilotes Air France de long
                            courrier, les élèves pilotes affinent leurs connaissances dans les 14 domaines de l’ATPL bien
                            au-delà des simples exigences de l’examen.
                        </p>
                        <p>
                            En parallèle, les EPL commencent à toucher du doigt les enjeux pratiques de leur métier avec des
                            préparations de vol et des séances de simulateur de pilotage, de l’avion léger à l’A320 en
                            passant par l’ATR 42. La formation sur le campus toulousain de l’ENAC permet aussi de comprendre
                            les contraintes des autres acteurs du monde aéronautique opérationnel grâce à la cohabitation
                            avec les ingénieurs, techniciens et contrôleurs, et la formation EPL inclut d’ailleurs diverses
                            simulations de contrôle, des visites d’ateliers de mécanique et de chaîne d’assemblage d’avions
                            de ligne.
                        </p>
                    </div>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
{{--                   <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                    <div class="relative space-y-6 lg:space-y-10 px-4 sm:px-6 lg:px-0">
                        <img
                            class="mx-auto rounded-lg shadow-xl"
                            width="490"
                            src="{{ asset('media/students-in-classroom.jpg') }}"
                            alt=""
                        />
                    </div>
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="784" />

            <div class="relative mt-12 sm:mt-16 lg:mt-24">
                <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="lg:col-start-2">
                        <x-public::feature-section-header>
                            <x-slot name="title">La phase de vol à vue</x-slot>
                            <x-slot name="description">
                                Une fois les certificats théoriques en poche, les EPL sont divisés en groupes réduits
                                pour partir dans leurs centres de formation pratique respectifs.
                            </x-slot>
                        </x-public::feature-section-header>

                        <img
                            class="lg:hidden mt-8 w-full rounded-lg shadow-xl"
                            src="{{ asset('media/tb20s-on-apron.jpg') }}"
                            alt="TB20 aircraft lined up on airfield apron"
                        />

                        <p class="mt-8 text-lg leading-6 font-medium text-gray-900">
                            Module maniabilité
                        </p>
                        <div class="mt-2 text-base text-justify text-gray-600 space-y-2">
                            <p>
                                La formation commence par l’apprentissage de la “mania”&nbsp;: il se décompose en 2 parties
                                sur 2 avions différents. Les élèves pilotes commencent par le Velis Electro, un avion
                                léger entièrement électrique sur lequel ils apprennent à décoller, atterrir, monter,
                                descendre, virer, réagir en cas de panne, de décrochage… C’est là qu’on apprend
                                réellement à “piloter” l’avion, et à communiquer avec les services de contrôle aérien
                                en une dizaine d’heures.
                            </p>
                            <p>
                                Une fois les bases du pilotage acquises sur le Velis, les EPL doivent reproduire tous
                                les exercices vus précédemment sur le TB20, un avion beaucoup plus lourd, puissant,
                                rapide et complexe. C’est sur cet avion que se déroule la majorité de la formation des
                                élèves pilotes de l’ENAC et ils doivent donc maîtriser son pilotage sur le bout des
                                doigts en une trentaine d’heures.
                            </p>
                            <div class="lg:hidden py-2">
                                <img
                                    class="w-full rounded-lg shadow-xl"
                                    src="{{ asset('media/lacher-solo.jpg') }}"
                                    alt="An EPL getting splashed by his classmates following his first solo flight"
                                />
                            </div>
                            <p>
                                C’est d’autant plus crucial étant donné que les EPL vont être “lâchés” durant cette
                                phase&nbsp;: pour la première fois ils vont voler seuls, sous l’œil attentif de leur
                                instructeur au sol, ils devront ramener l’avion par eux-mêmes grâce à leurs seules
                                compétences.
                            </p>
                        </div>

                        <p class="mt-8 text-lg leading-6 font-medium text-gray-900">
                            Module Advanced UPRT
                        </p>
                        <div class="mt-2 text-base text-justify text-gray-600 space-y-2">
                            <p>
                                L’Upset Prevention and Recovery Training avancé est la phase la plus courte de la
                                formation, mais n’en est pas moins importante.
                            </p>
                            <div class="lg:hidden py-2">
                                <img
                                    class="w-full rounded-lg shadow-xl"
                                    src="{{ asset('media/cap10-startup.jpg') }}"
                                    alt="A CAP 10 aircraft starting up"
                                />
                            </div>
                            <p>
                                C’est au cours de 3h de vols sur le CAP10, un avion français sur lequel se sont formés
                                de nombreux pilotes de voltige, que les EPL découvrent les positions inusuelles avancées
                                telles que les vrilles, et surtout comment en sortir et éviter d’y entrer. C’est aussi
                                l’occasion d’affiner son pilotage en profitant de la technicité du train classique, et
                                en effectuant des figures de voltige de base comme la boucle ou le baquet.
                            </p>
                        </div>

                        <p class="mt-8 text-lg leading-6 font-medium text-gray-900">
                            Module navigation
                        </p>
                        <div class="mt-2 text-base text-justify text-gray-600 space-y-2">
                            <p>
                                Maintenant que les EPL savent piloter un avion complexe, gérer les pannes et pertes de
                                contrôle et communiquer en fréquence, il faut apprendre la navigation. Aller d’un point
                                A à un point B, c'est le cœur du métier de pilote de ligne, et cela implique tout un lot
                                de compétences. Les élèves pilotes vont d’abord se familiariser avec la préparation et
                                la conduite d’un vol à la carte, à gérer leur carburant, corriger le temps de vol en
                                tenant compte du vent, du trafic, et de toutes les contraintes liées à l’espace aérien
                                complexe dans lequel ils évoluent.
                            </p>
                            <div class="lg:hidden py-2">
                                <img
                                    class="w-full rounded-lg shadow-xl"
                                    src="{{ asset('media/tb20-vfr-map.jpg') }}"
                                    alt="An EPL student in the cockpit reviewing his VFR map during a navigation flight"
                                />
                            </div>
                            <p>
                                À la navigation de base vont ensuite s’ajouter l’utilisation des systèmes comme le GPS
                                ou le pilote automatique, les aérodromes à forte densité de trafic, puis les exercices
                                face à l’imprévu lorsqu’on est loin de son aérodrome d’origine&nbsp;: les pannes en campagne
                                et déroutements vont améliorer la réactivité et la prise de décision des EPL avant
                                qu’ils ne partent seuls en navigation pour une petite vingtaine d’heures de navigations
                                en solo au dessus de parties inconnues du territoire. Au total, plus de cinquante heures
                                de navigation VFR seront effectuées par chaque EPL avant de passer au vol aux
                                instruments.
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
{{--                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                        <div class="hidden lg:block relative space-y-6 lg:space-y-10">
                            <img
                                class="mx-auto lg:rounded-lg lg:shadow-xl"
                                width="490"
                                src="{{ asset('media/tb20s-on-apron.jpg') }}"
                                alt="TB20 aircraft lined up on airfield apron"
                            />
                            <img
                                class="mx-auto lg:rounded-lg lg:shadow-xl"
                                width="490"
                                src="{{ asset('media/lacher-solo.jpg') }}"
                                alt="An EPL getting splashed by his classmates following his first solo flight"
                            />
                            <img
                                class="mx-auto lg:rounded-lg lg:shadow-xl"
                                width="490"
                                src="{{ asset('media/cap10-startup.jpg') }}"
                                alt="A CAP 10 aircraft starting up"
                            />
                            <img
                                class="mx-auto lg:rounded-lg lg:shadow-xl"
                                width="490"
                                src="{{ asset('media/tb20-vfr-map.jpg') }}"
                                alt="An EPL student in the cockpit reviewing his VFR map during a navigation flight"
                            />
                        </div>
                    </div>
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute left-full transform -translate-x-1/2 translate-y-12" width="404" height="360" />

            <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <x-public::feature-section-header>
                        <x-slot name="title">La phase IRSE</x-slot>
                    </x-public::feature-section-header>

                    <div class="mt-8 text-base text-justify text-gray-600 space-y-2">
                        <p>
                            Avoir la tête dans les nuages, c’est bien, en faire son métier c’est mieux. Désormais à
                            l’aise avec le pilotage, il est temps pour les EPL de faire un premier pas dans le monde
                            professionnel, là où les pilotes n’attendent pas les tempêtes de ciel bleu pour
                            décoller. Mais pas de panique ! Avant de se lancer pour de bon, passage au simulateur
                            obligatoire pour appréhender les subtilités du vol aux instruments sur une réplique de
                            cockpit du TB20.
                        </p>
                        <p>
                            Les élèves pilotes se familiarisent avec les cartes et les procédures utilisées par les
                            pilotes en compagnie aérienne et apprennent à naviguer sans repères visuels. Avec 25
                            heures de <span class="italic">simu</span> (dont une heure d’évaluation), les EPL ont
                            l’expérience pour partir un peu partout et par (presque) toutes les conditions
                            météorologiques.
                        </p>
                        <p>
                            Pendant 40h, ils sillonnent la France et en profitent pour s’habituer à parler anglais à
                            la radio. Comme d’habitude, un test vient valider les compétences acquises.
                        </p>
                        <p>
                            Et tant qu’à faire, un pilote sachant travailler par visibilité réduite peut bien
                            travailler de nuit, non ? Dans une atmosphère incomparable, les élèves réalisent 5
                            heures de vol nocturne, découvrant ainsi les joies des horaires décalés mais surtout
                            la magie nouvelle d’un ciel étoilé.
                        </p>
                    </div>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0">
                    {{--                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                    <div class="relative space-y-6 lg:space-y-10 px-4 sm:px-6 lg:px-0">
                        <img
                            class="mx-auto rounded-lg shadow-xl"
                            width="490"
                            src="{{ asset('media/tb20-imc-curtain.jpg') }}"
                            alt="View from inside TB20 cockpit with instrument training curtain pulled"
                        />
                    </div>
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="360" />

            <div class="relative mt-12 sm:mt-16 lg:mt-24">
                <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="lg:col-start-2">
                        <x-public::feature-section-header>
                            <x-slot name="title">La phase CPL</x-slot>
                        </x-public::feature-section-header>

                        <div class="mt-8 text-base text-justify text-gray-600 space-y-2">
                            <p>
                                Retour au vol à vue. Eh oui, être pilote c’est savoir s’adapter ! Forts de leur
                                expérience (désormais conséquente) sur TB20, les élèves pilotes repartent en navigations
                                plus classiques. Mais cette fois, il n’est plus vraiment question d’apprendre à piloter
                                ou à se repérer.
                            </p>
                            <p>
                                Non, cette phase vise à renforcer le professionnalisme de futurs commandants de bord.
                                Chaque vol est l’occasion de réviser ou faire pour la première fois des exercices&nbsp;: des
                                pannes, des déroutements, souvent les deux en même temps, pour préparer l’examen du CPL
                                (Commercial Pilot Licence) et aiguiser l’une des compétences clés d’un pilote
                                professionnel&nbsp;: la prise de décision.
                            </p>
                            <p>
                                C’est aussi l’occasion pour les EPL, plus expérimentés que jamais, de repartir en solo
                                et réaliser des navigations plus ambitieuses, dont une de minimum 300 NM (environ
                                550&nbsp;km). Une phase courte mais riche en émotions et en souvenirs, ponctuée par le
                                passage du CPL, premier examen officiel, avec à la clé la première licence
                                professionnelle.
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
                        {{--                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                        <div class="relative space-y-6 lg:space-y-10 px-4 sm:px-6 lg:px-0">
                            <img
                                class="mx-auto rounded-lg shadow-xl"
                                width="490"
                                src="{{ asset('media/tb20-landing.jpg') }}"
                                alt="A TB20 seen landing on a runway"
                            />
                        </div>
                    </div>
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute left-full transform -translate-x-1/2 translate-y-12" width="404" height="360" />

            <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div class="relative">
                    <x-public::feature-section-header>
                        <x-slot name="title">La phase IRME</x-slot>
                    </x-public::feature-section-header>

                    <div class="mt-8 text-base text-justify text-gray-600 space-y-2">
                        <p>
                            Les EPL ont à présent fait le tour du TB20 et s’attaquent à plus gros, plus lourd, plus
                            rapide. Sur le Beechcraft Baron 58, ils expérimentent le vol sur bimoteur tout en revenant
                            au vol aux instruments.
                        </p>
                        <p>
                            Là encore, un passage de 15 heures sur simulateur permet de se rafraîchir la mémoire sur les
                            procédures IFR (Instrument Flight Rules), et assimiler le pilotage d’un nouvel avion, doté
                            d’une avionique ultra moderne Garmin G500 TXi. Après un examen en simulateur, les élèves
                            peuvent enfin décoller, avec cette fois deux moteurs et donc deux fois plus de manettes à
                            manipuler.
                        </p>
                        <p>
                            En se formant aux pannes moteurs pendant toutes les phases du vol, ils apprennent à gérer
                            des situations de propulsion asymétrique et font un pas de plus vers l’aviation commerciale.
                        </p>
                        <p>
                            La QC MEP (qualification de classe multimoteur à pistons) et l’IRME (qualification de vol
                            aux instruments sur multimoteur) sont deux examens officiels qui viennent clôturer cette
                            phase de 30 heures, au cours de laquelle les EPL mettent à profit les capacités de cet avion
                            exceptionnel pour s’évader encore plus loin.
                        </p>
                    </div>
                </div>

                <div class="mt-10 -mx-4 relative lg:mt-0" aria-hidden="true">
{{--                   <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                    <div class="relative space-y-6 lg:space-y-10 px-4 sm:px-6 lg:px-0">
                        <img
                            class="mx-auto rounded-lg shadow-xl"
                            width="490"
                            src="{{ asset('media/be58-approach.jpg') }}"
                            alt="An EPL flying an approach in a twin-engine BE58"
                        />
                    </div>
                </div>
            </div>

           <x-public::pattern.dots class="hidden lg:block absolute right-full transform translate-x-1/2 translate-y-12" width="404" height="360" />

            <div class="relative mt-12 sm:mt-16 lg:mt-24">
                <div class="lg:grid lg:grid-flow-row-dense lg:grid-cols-2 lg:gap-8 lg:items-center">
                    <div class="lg:col-start-2">
                        <x-public::feature-section-header>
                            <x-slot name="title">La phase MCC/JOC</x-slot>
                        </x-public::feature-section-header>

                        <div class="mt-8 text-base text-justify text-gray-600 space-y-2">
                            <p>
                                Souvent considérée comme la phase la plus intéressante de la formation, la MCC parachève
                                la formation EPL&nbsp;: transformant les élèves pilotes compétents sur avions monopilotes en
                                pilotes de ligne capable de travailler en équipage.
                            </p>
                            <p>
                                En effet, les avions commerciaux, plus grands, plus rapides, plus complexes, nécessitent
                                la coopération de deux pilotes, qui ont des parcours différents et qui bien souvent ne
                                se connaissent préalablement pas. La dernière étape de formation pour un EPL est donc de
                                se former aux techniques de vol en équipage au cours d’un stage MCC/JOC (Multi-Crew
                                Cooperation / Jet Orientation Course), qui lui permettra de s’intégrer parfaitement dans
                                n’importe quel cockpit.
                            </p>
                            <p>
                                Pour cela, retour à Toulouse sur le campus principal de l’ENAC, et c’est sur un
                                simulateur Airbus A320 certifié que se déroulent les 60 heures de cette phase. Assis un
                                coup à droite, un coup à gauche, les élèves pilotes alternent en binôme les différentes
                                fonctions d’un pilote en équipage tout en découvrant le pilotage d’un avion commercial
                                répandu dans le monde entier.
                            </p>
                            <p>
                                En excédant largement les exigences horaires réglementaires de ce stage, l’ENAC assure
                                aux EPL une préparation optimale aux défis présentés par le transport aérien commercial.
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 -mx-4 relative lg:mt-0 lg:col-start-1">
{{--                       <x-public::pattern.dots class="absolute left-1/2 transform -translate-x-1/2 translate-y-16 lg:hidden" width="784" height="404" />--}}

                        <div class="relative space-y-6 lg:space-y-10 px-4 sm:px-6 lg:px-0">
                            <img
                                class="mx-auto rounded-lg shadow-xl"
                                width="490"
                                src="{{ asset('media/a320-sim-smiles.jpg') }}"
                                alt="A couple of EPL students smiling at the camera from their seats in the A320 simulator"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-12 sm:mt-16 lg:mt-24 max-w-md px-4 text-center sm:px-6 sm:max-w-3xl lg:px-8">
                <x-public::feature-section-header>
                    <x-slot name="title">
                        Et voilà, c’est fini. Ou plutôt&nbsp;: tout commence.
                    </x-slot>
                    <x-slot name="description" class="max-w-prose mx-auto">
                        À ce stade, chaque EPL a déjà des milliers de choses à raconter, mais il lui reste encore à
                        prendre son envol et construire sa propre histoire.
                    </x-slot>
                </x-public::feature-section-header>
            </div>
        </div>
    </div>

</x-public::layout>
