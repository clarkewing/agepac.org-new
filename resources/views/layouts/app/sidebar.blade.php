<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-700 bg-universe">
        {{-- Dark styles on the navy background; the user menu stays out so it follows the page theme. --}}
        <div class="dark contents">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            {{-- TODO: Add wire:navigate --}}
            <a href="{{ route('dashboard') }}" class="me-5 mb-2 flex items-center space-x-2 rtl:space-x-reverse">
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group class="grid">
                    {{-- TODO: Add wire:navigate --}}
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('navigation.sidebar.dashboard') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <x-sidebar-nav />
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                @can('access-admin-panel')
                    <flux:navlist.item icon="shield-check" href="{{ route('filament.admin.pages.dashboard') }}">
                        {{ __('navigation.sidebar.admin') }}
                    </flux:navlist.item>
                @endcan
                <flux:navlist.item icon="globe-alt" href="{{ route('public.home') }}" target="_blank">
                    {{ __('navigation.sidebar.public-site') }}
                </flux:navlist.item>
            </flux:navlist>
        </div>

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile
                class="dark"
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                avatar:color="auto"
                :avatar:color:seed="auth()->id()"
                icon:trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar
                                size="sm"
                                :initials="auth()->user()->initials()"
                                color="auto"
                                :color:seed="auth()->id()"
                            />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    {{-- TODO: Add wire:navigate --}}
                    <flux:menu.item :href="route('settings.profile')" icon="cog">
                        {{ __('navigation.user-menu.settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('navigation.user-menu.logout') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="bg-universe lg:hidden">
        <flux:sidebar.toggle class="dark lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile
                class="dark"
                :initials="auth()->user()->initials()"
                avatar:color="auto"
                :avatar:color:seed="auth()->id()"
                icon-trailing="chevron-down"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar
                                size="sm"
                                :initials="auth()->user()->initials()"
                                color="auto"
                                :color:seed="auth()->id()"
                            />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    {{-- TODO: Add wire:navigate --}}
                    <flux:menu.item :href="route('settings.profile')" icon="cog">
                        {{ __('navigation.user-menu.settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('navigation.user-menu.logout') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
    @persist('toast')
        <flux:toast />
    @endpersist
</body>
</html>
