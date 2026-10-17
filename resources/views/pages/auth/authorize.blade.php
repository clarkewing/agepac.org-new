@use('Laravel\Head\Facades\Head')

@php(Head::title(__('auth.authorize.title')))

<x-layouts::auth>
    <div class="flex flex-col gap-6">
        @if ($user->canSignInToApps())
            <x-auth-header
                :title="__('auth.authorize.heading', ['app' => $client->name])"
                :description="__('auth.authorize.description', ['app' => $client->name])"
            />
        @else
            @php([$reason, $url] = match (true) {
                ! $user->hasVerifiedEmail() => ['unverified', route('verification.notice')],
                $user->isPendingApproval() => ['pending-approval', route('pending-approval')],
                default => ['membership', route('settings.membership')],
            })

            <x-auth-header
                :title="__('auth.authorize.ineligible.heading', ['app' => $client->name])"
                :description="__('auth.authorize.ineligible.reasons.'.$reason.'.text')"
            />
        @endif

        <div class="flex flex-col gap-3">
            @if ($user->canSignInToApps())
                <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                    @csrf
                    <input type="hidden" name="state" value="{{ $request->state }}" />
                    <input type="hidden" name="client_id" value="{{ $client->getKey() }}" />
                    <input type="hidden" name="auth_token" value="{{ $authToken }}" />

                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('auth.authorize.approve') }}
                    </flux:button>
                </form>
            @else
                <flux:button variant="primary" :href="$url" class="w-full">
                    {{ __('auth.authorize.ineligible.reasons.'.$reason.'.action') }}
                </flux:button>
            @endif

            {{-- Sends the member back to the app, which learns they did not sign in. --}}
            <form method="POST" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="state" value="{{ $request->state }}" />
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}" />
                <input type="hidden" name="auth_token" value="{{ $authToken }}" />

                <flux:button variant="ghost" type="submit" class="w-full">
                    {{ $user->canSignInToApps() ? __('auth.authorize.deny') : __('auth.authorize.ineligible.back', ['app' => $client->name]) }}
                </flux:button>
            </form>
        </div>

        {{-- prompt=login has Passport log the member out, then back in to resume this request. --}}
        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.authorize.signed-in-as', ['name' => $user->name]) }}</span>
            <flux:link :href="$request->fullUrlWithQuery(['prompt' => 'login'])">{{ __('auth.authorize.switch-account') }}</flux:link>
        </flux:subheading>
    </div>
</x-layouts::auth>
