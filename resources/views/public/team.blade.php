<x-public::layout title="Notre équipe">
    <x-slot
        name="header"
        backdrop="{{ asset('media/auditorium-audience.jpg') }}"
        alt="EPL graduates in uniform discussing among themselves in auditorium seats"
    >
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            Notre équipe
        </h1>
    </x-slot>

    <div class="bg-white">
        <div class="mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8 lg:py-12">
            <div class="space-y-12">
                <x-public::leadership.section-heading>
                    <x-slot name="title">Notre Bureau</x-slot>
                    <x-slot name="description">
                        Au cœur de la vie quotidienne de notre Association, les membres du Bureau sont les
                        porte&#8209;paroles de l’AGEPAC.
                    </x-slot>
                </x-public::leadership.section-heading>

                <ul role="list" class="space-y-12 lg:grid lg:grid-cols-2 lg:items-start lg:gap-x-8 lg:gap-y-12 lg:space-y-0">
                    @foreach(config('public-site.board') as $boardMember)
                        <x-public::leadership.card-detailed
                            name="{{ $boardMember['name'] }}"
                            title="{{ $boardMember['title'] }}"
                            promotion="{{ $boardMember['promotion'] }}"
                            job="{{ $boardMember['job'] }}"
                            linkedin-url="{{ $boardMember['linkedin'] }}"
                        >
                            <x-slot name="description">{{ $boardMember['bio'] }}</x-slot>
                        </x-public::leadership.card-detailed>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="text-center mx-auto py-6 px-4 max-w-7xl sm:px-6 lg:px-8 lg:py-12">
            <div class="space-y-12">
                <x-public::leadership.section-heading>
                    <x-slot name="title">Notre Conseil</x-slot>
                    <x-slot name="description">
                        Le Conseil de l’AGEPAC est constitué de Représentants de Promotions et d’Anciens Présidents. Ils
                        votent des motions en dehors des Assemblées Générales.
                    </x-slot>
                </x-public::leadership.section-heading>

                <ul role="list" class="mx-auto grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-4 md:gap-x-6 lg:gap-x-8 lg:gap-y-12 xl:grid-cols-6">
                    @foreach(config('public-site.council') as $councilMember)
                        <x-public::leadership.card-simple
                            name="{{ $councilMember['name'] }}"
                            title="{!! $councilMember['title'] !!}"
                            photo="{{ $councilMember['photo'] ?? '' }}"
                        />
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-public::layout>
