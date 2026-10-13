@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.verify-email.title')))

<x-layouts::auth>
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {!! __('auth.verify-email.heading', ['email' => '<strong>'.e(auth()->user()->email).'</strong>']) !!}
        </flux:text>

        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.verify-email.change-prompt') }}</span>
            <flux:link
                :href="route('settings.profile')"
                wire:navigate
            >{{ __('auth.verify-email.change-link') }}</flux:link>
        </flux:subheading>

        @if (session('status') == 'verification-link-sent')
            <flux:callout
                variant="success"
                icon="check-circle"
                :heading="__('auth.verify-email.status.verification-link-sent', ['email' => auth()->user()->email])"
            />
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf

                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('auth.verify-email.action') }}
                </flux:button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <flux:button variant="ghost" type="submit" class="cursor-pointer text-sm">
                    {{ __('auth.verify-email.logout-link') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
