<div class="flex flex-col gap-6">
    <flux:heading class="text-center" size="xl">{{ __('Welcome back') }}</flux:heading>

    <div class="space-y-4">
        @env('local')
            <x-login-link />
        @endenv
    </div>

    <flux:separator text="or" />

    <div class="flex flex-col gap-6">
        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form wire:submit="login" class="flex flex-col gap-6">
            <!-- Email -->
            <flux:input
                wire:model="email"
                :label="__('Email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:field>
                <div class="mb-3 flex justify-between">
                    <flux:label>Password</flux:label>

                    @if (Route::has('password.request'))
                        <flux:link variant="subtle" class="text-sm" :href="route('password.request')" wire:navigate>
                            {{ __('Forgot password?') }}
                        </flux:link>
                    @endif
                </div>

                <flux:input
                    wire:model="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                    placeholder="Your password"
                />
            </flux:field>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <flux:subheading class="text-center">
                <span>{{ __('First time around here?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
            </flux:subheading>
        @endif
    </div>
</div>
