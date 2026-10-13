@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.login.title')))

<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <flux:heading class="text-center" size="xl">{{ __('auth.login.heading') }}</flux:heading>

        <div class="flex flex-col gap-6">
            <!-- Session Status -->
            @if (session('status'))
                <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf

                <!-- Email -->
                <flux:input
                    name="email"
                    :value="old('email')"
                    :label="__('fields.email.label')"
                    type="email"
                    required
                    :autofocus="blank(old('email'))"
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <!-- Password -->
                <flux:field>
                    <div class="flex justify-between">
                        <flux:label>{{ __('fields.password.label') }}</flux:label>

                        @if (Route::has('password.request'))
                            <flux:link variant="subtle" class="text-sm" :href="route('password.request')" wire:navigate>
                                {{ __('auth.login.forgot-password') }}
                            </flux:link>
                        @endif
                    </div>

                    <flux:input
                        name="password"
                        type="password"
                        required
                        :autofocus="filled(old('email'))"
                        autocomplete="current-password"
                        :placeholder="__('fields.password.placeholder')"
                        viewable
                    />
                </flux:field>

                <!-- Remember Me -->
                <flux:checkbox name="remember" :checked="old('remember')" :label="__('auth.login.remember')" />

                <div class="flex items-center justify-end">
                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full"
                    >{{ __('auth.login.action') }}</flux:button>
                </div>
            </form>

            @env('local')
                <flux:separator text="or" />

                <div class="space-y-4">
                    <x-login-link />
                </div>
            @endenv

            @if (Route::has('register'))
                <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
                    <span>{{ __('auth.login.register-prompt') }}</span>
                    <flux:link :href="route('register')" wire:navigate>{{ __('auth.login.register-link') }}</flux:link>
                </flux:subheading>
            @endif
        </div>
    </div>
</x-layouts::auth>
