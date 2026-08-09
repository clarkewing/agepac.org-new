<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Laravel\Head\Facades\Head;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function rendering(): void
    {
        Head::title(__('navigation.settings.profile').' - '.__('settings.title'));
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('settings.profile.heading')" :subheading="__('settings.profile.subheading')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="name"
                :label="__('fields.name.label')"
                type="text"
                required
                autofocus
                autocomplete="name"
            />

            <div>
                <flux:input
                    wire:model="email"
                    :label="__('fields.email.label')"
                    type="email"
                    required
                    autocomplete="email"
                />

                @if (auth()->user() instanceof MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div class="mt-4">
                        <flux:text>
                            <flux:icon name="exclamation-triangle" class="mr-px inline h-5 w-5 text-amber-400" />
                            {{ __('settings.profile.email-verification.prompt') }}

                            <flux:link
                                class="cursor-pointer text-sm"
                                wire:click.prevent="resendVerificationNotification"
                            >
                                {{ __('settings.profile.email-verification.action') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="!dark:text-green-400 mt-2 font-medium !text-green-600">
                                {{ __('settings.profile.email-verification.status.verification-link-sent') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('settings.profile.action') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated" />
            </div>
        </form>

        <livewire:pages::settings.delete-user-form />
    </x-settings.layout>
</section>
