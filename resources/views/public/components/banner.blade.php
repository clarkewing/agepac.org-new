@if(today()->isBefore('2023-03-01'))
    <div class="sticky top-0 z-100 bg-vermilion-400 shadow-lg shadow-gray-600/25">
        <div class="mx-auto max-w-7xl py-3 px-3 sm:px-6 md:py-4 lg:px-8">
            <div class="flex flex-wrap items-center justify-between">
                <div class="flex flex-1 items-center max-w-full">
                <span class="flex rounded-lg px-2 py-1">
                    <x-heroicon-o-megaphone class="h-8 md:h-10 text-white" aria-hidden="true" />
                </span>
                    <p class="ml-3 truncate font-medium text-white">
                        <span class="lg:hidden">Nouveaux critères EPL/U en vigueur</span>
                        <span class="hidden lg:inline">Le concours EPL/U évolue avec de nouveaux critères !</span>
                    </p>
                </div>
                <div class="mt-2 w-full shrink-0 sm:mt-0 sm:w-auto">
                    <a
                        href="/epl/selection#new-epl-u-criteria"
                        class="flex items-center justify-center rounded-md border border-transparent bg-white px-4 py-2 text-sm font-medium text-vermilion-400 shadow-xs hover:bg-vermilion-50"
                    >
                        En savoir plus
                    </a>
                </div>
            </div>
        </div>
    </div>

@endif
