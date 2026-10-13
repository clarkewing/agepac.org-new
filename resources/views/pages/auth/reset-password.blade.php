@use('Illuminate\Validation\Rules\Password')
@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.reset-password.title')))

<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('auth.reset-password.heading')"
            :description="__('auth.reset-password.description')"
        />

        <!-- Session Status -->
        @if (session('status'))
            <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}" />

            <!-- Email Address -->
            <flux:input
                name="email"
                :value="old('email', $request->email)"
                :label="__('fields.email.label')"
                type="email"
                required
                readonly
                autocomplete="email"
                :placeholder="__('fields.email.placeholder')"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('fields.password.label')"
                type="password"
                required
                autofocus
                autocomplete="new-password"
                :placeholder="__('fields.password.placeholder')"
                :passwordrules="Password::defaults()->toPasswordRulesString()"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('fields.password-confirmation.label')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('fields.password-confirmation.placeholder')"
                :passwordrules="Password::defaults()->toPasswordRulesString()"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('auth.reset-password.action') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
