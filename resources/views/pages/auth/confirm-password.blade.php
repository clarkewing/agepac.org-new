@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.confirm-password.title')))

<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.confirm-password.heading')"
            :description="__('auth.confirm-password.description')"
        />

        <!-- Session Status -->
        @if (session('status'))
            <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
        @endif

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('fields.password.label')"
                type="password"
                required
                autofocus
                autocomplete="current-password"
                :placeholder="__('fields.password.placeholder')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('auth.confirm-password.action') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
