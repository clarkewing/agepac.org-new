<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component
{
    public function mount(): void
    {
        if (Auth::user()->isApproved()) {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function rendering(): void
    {
        Head::title(__('auth.pending-approval.title'));
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.pending-approval.heading')" />

    <div class="flex w-full flex-col text-center">
        <flux:heading size="lg">{{ __('auth.pending-approval.greeting', ['name' => auth()->user()->first_name]) }}</flux:heading>
        <flux:subheading>{{ __('auth.pending-approval.description') }}</flux:subheading>
    </div>

    <flux:card size="sm" variant="soft">
        <flux:timeline align="baseline">
            <flux:timeline.item>
                <flux:timeline.indicator color="emerald">
                    <flux:icon.check variant="micro" />
                </flux:timeline.indicator>

                <flux:timeline.content>
                    <flux:heading>{{ __('auth.pending-approval.steps.created') }}</flux:heading>
                </flux:timeline.content>
            </flux:timeline.item>

            <flux:timeline.item>
                @if (auth()->user()->hasVerifiedEmail())
                    <flux:timeline.indicator color="emerald">
                        <flux:icon.check variant="micro" />
                    </flux:timeline.indicator>
                @else
                    <flux:timeline.indicator color="amber">
                        <flux:icon.envelope variant="micro" />
                    </flux:timeline.indicator>
                @endif

                <flux:timeline.content>
                    @if (auth()->user()->hasVerifiedEmail())
                        <flux:heading>{{ __('auth.pending-approval.steps.verified') }}</flux:heading>
                        <flux:subheading>{{ auth()->user()->email }}</flux:subheading>
                    @else
                        <flux:heading>{{ __('auth.pending-approval.steps.verify') }}</flux:heading>
                        <flux:subheading>{{ auth()->user()->email }}</flux:subheading>
                        <flux:subheading>
                            <flux:link :href="route('verification.notice')">{{ __('auth.pending-approval.steps.verify-link') }}</flux:link>
                        </flux:subheading>
                    @endif
                </flux:timeline.content>
            </flux:timeline.item>

            <flux:timeline.item>
                <flux:timeline.indicator color="amber">
                    <flux:icon.clock variant="micro" />
                </flux:timeline.indicator>

                <flux:timeline.content>
                    <flux:heading>{{ __('auth.pending-approval.steps.approval') }}</flux:heading>
                    <flux:subheading>{{ __('auth.pending-approval.steps.approval-hint') }}</flux:subheading>
                </flux:timeline.content>
            </flux:timeline.item>
        </flux:timeline>
    </flux:card>

    <div class="flex flex-col items-center gap-3">
        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.pending-approval.help-prompt') }}</span>
            <flux:link :href="route('pages.show', 'help')">{{ __('auth.pending-approval.help-link') }}</flux:link>
        </flux:subheading>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <flux:button variant="ghost" type="submit" class="cursor-pointer text-sm">
                {{ __('auth.pending-approval.logout-link') }}
            </flux:button>
        </form>
    </div>
</div>
