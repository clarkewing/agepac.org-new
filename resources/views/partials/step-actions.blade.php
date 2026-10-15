<div class="flex items-center justify-between gap-3">
    @if ($this->hasPreviousStep())
        <flux:button variant="ghost" icon="arrow-left" wire:click="previousStep">{{ __('wizard.back') }}</flux:button>
    @endif

    @if (isset($lastStepLabel) && ! $this->hasNextStep())
        <flux:button type="submit" variant="primary" :class="$this->hasPreviousStep() ? 'ms-auto' : 'w-full'">
            {{ $lastStepLabel }}
        </flux:button>
    @else
        <flux:button
            type="submit"
            variant="primary"
            icon:trailing="arrow-right"
            :class="$this->hasPreviousStep() ? 'ms-auto' : 'w-full'"
        >
            {{ __('wizard.continue') }}
        </flux:button>
    @endif
</div>
