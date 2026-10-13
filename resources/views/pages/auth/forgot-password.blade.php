@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.forgot-password.title')))

<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.forgot-password.heading')"
            :description="__('auth.forgot-password.description')"
        />

        <!-- Session Status -->
        @if (session('status'))
            <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :value="old('email')"
                :label="__('fields.email.label')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('auth.forgot-password.action') }}
            </flux:button>
        </form>

        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.forgot-password.login-prompt') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('auth.forgot-password.login-link') }}</flux:link>
        </flux:subheading>
    </div>
</x-layouts::auth>
