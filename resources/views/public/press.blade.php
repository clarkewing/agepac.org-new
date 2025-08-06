<x-public::layout title="Presse & médias">
    <x-slot
        name="header"
        backdrop="{{ asset('media/cameraman-in-sim.jpg') }}"
        alt="A camera crew filming an interview"
    >
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            Espace presse et médias
        </h1>
        <p class="mt-6 text-xl text-cyan-100 max-w-3xl">
            Vous souhaitez contacter notre service de presse ? Vous avez des questions sur
            l’AGEPAC, la formation Élève Pilote de Ligne de l’ENAC, ou vous souhaitez obtenir
            des photos ? Vous trouverez ici des informations  à ce sujet.
        </p>
    </x-slot>

    <div class="bg-white">
        <div class="max-w-md mx-auto py-24 px-4 sm:max-w-3xl sm:py-32 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="divide-y divide-gray-200 space-y-16">
                <section class="pb-16 lg:grid lg:grid-cols-3 lg:gap-8" aria-labelledby="contacts-heading">
                    <h2 id="contacts-heading" class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        Contacts
                    </h2>
                    <div class="max-w-3xl space-y-8 mt-8 lg:mt-0 lg:col-span-2">
                        <div>
                            <h3 class="text-2xl font-extrabold tracking-tight text-gray-900">
                                Questions médias
                            </h3>
                            <p class="mt-4 text-lg text-gray-600">
                                Vous êtes journaliste et vous souhaitez obtenir des informations sur l’AGEPAC ?
                                Vous pouvez nous joindre par email :
                                <a class="underline hover:text-gray-700" href="mailto:media@agepac.org">media@agepac.org</a>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-2xl font-extrabold tracking-tight text-gray-900">
                                Réseaux sociaux
                            </h3>
                            <p class="mt-4 text-lg text-gray-600">
                                Les réseaux sociaux sont votre tasse de thé et vous souhaitez collaborer avec l’AGEPAC ?
                                Entrez en contact avec notre community manager :
                                <a class="underline hover:text-gray-700" href="mailto:social@agepac.org">social@agepac.org</a>
                            </p>
                        </div>
                    </div>
                </section>

                <section class="lg:grid lg:grid-cols-3 lg:gap-8" aria-labelledby="terms-heading">
                    <h2 id="terms-heading" class="text-2xl font-extrabold text-gray-900 sm:text-3xl">
                        Conditions générales
                    </h2>
                    <div class="max-w-3xl text-lg text-gray-600 leading-7 mt-8 lg:mt-0 lg:col-span-2">
                        <p>
                            Les conditions générales auxquelles votre demande doit obligatoirement répondre :
                        </p>
                        <ul class="mt-2 ml-6 list-disc">
                            <li>
                                L’AGEPAC évalue chaque demande individuellement. Les prises de vue ne peuvent contenir aucun
                                élément susceptible de pouvoir affecter l’AGEPAC et/ou ses partenaires.
                            </li>
                            <li>
                                L’AGEPAC se réserve le droit d'interrompre tout tournage ou interview.
                            </li>
                            <li>
                                Une demande doit être soumise au minimum une semaine avant le jour d’un interview ou de tournage.
                            </li>
                            <li>
                                Nos membres se réservent le choix de ne pas vouloir apparaître à l'écran et/ou de conserver
                                leur anonymat lors de toute intervention.
                            </li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-public::layout>
