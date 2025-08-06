<div x-data="{ open: false }">
    <span x-on:click="open = true">
        {{ $trigger }}
    </span>

    <template x-teleport="body">
        <!-- Mobile navigation modal -->
        <div
            x-show="open"
            style="display: none"
            x-on:keydown.escape.prevent.stop="open = false"
            role="dialog"
            aria-modal="true"
            aria-label="Navigation menu"
            class="fixed inset-0 overflow-y-auto z-10"
        >
            <!-- Overlay -->
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 bg-black/50"
            ></div>

            <!-- Panel -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-on:click="open = false"
                class="absolute z-10 top-0 inset-x-0 p-2 origin-top-right md:hidden"
            >
                <div
                    x-on:click.stop
                    x-trap.noscroll.inert="open"
                    class="rounded-lg shadow-lg ring-1 ring-black/5 bg-white divide-y-2 divide-gray-50"
                >
                    <div class="pt-5 pb-6 px-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <x-public::application-logo class="h-8 w-auto" />
                            </div>
                            <div class="-mr-2">
                                <button
                                    type="button"
                                    @click="open = false"
                                    class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                >
                                    <span class="sr-only">Close menu</span>
                                    <x-heroicon-o-x-mark class="h-6 w-6" />
                                </button>
                            </div>
                        </div>
                        <div class="mt-6 space-y-1">
                            <a href="{{ route('public.home') }}" class="block rounded-md p-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                                Accueil
                            </a>
                            <a href="{{ route('public.epl.selection') }}" class="block rounded-md p-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                                La Sélection EPL
                            </a>
                            <a href="{{ route('public.epl.training') }}" class="block rounded-md p-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                                La Formation EPL
                            </a>
                            <a href="{{ route('public.association.about') }}" class="block rounded-md p-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                                Association
                            </a>
                            <a href="{{ route('public.contact') }}" class="block rounded-md p-2 text-base text-gray-900 font-medium hover:bg-gray-100 hover:text-gray-800">
                                Nous Recruter
                            </a>
                        </div>
                    </div>
                    <div class="py-6 px-5 space-y-6">
{{--                        <a href="#" class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-xs text-base font-medium text-white bg-red-700 hover:bg-red-600">--}}
{{--                            Faire un don--}}
{{--                        </a>--}}
                        <div>
                            <p class="text-center text-sm font-medium text-gray-500">
                                Ancien élève ? Encore en formation ?
                            </p>
                            <div class="mt-1 flex justify-center">
                                <a href="{{ route('dashboard') }}" class="flex items-center px-2 text-vermilion-400 hover:text-vermilion-500">
                                    <x-heroicon-o-user-circle class="shrink-0 h-5 w-5" />
                                    <span class="ml-2">Espace Membres</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
