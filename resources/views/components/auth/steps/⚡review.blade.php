<?php

use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Livewire\Traits\SfcStep;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Computed;
use Propaganistas\LaravelPhone\PhoneNumber;
use Spatie\LivewireWizard\Components\StepComponent;

new class extends StepComponent
{
    // TODO: Drop once spatie/laravel-livewire-wizard#159 ships.
    use SfcStep;

    /**
     * Send the member to the step that collects a field, to come back here
     * once it is saved.
     */
    public function edit(string $field): void
    {
        $this->dispatchDehydrated('editField', field: $field, currentStepState: $this->state()->currentStep())
            ->to($this->wizardClassName);
    }

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function answers(): array
    {
        return array_merge(...array_map($this->state()->forStep(...), $this->allStepNames));
    }

    #[Computed]
    public function phone(): string
    {
        $phone = $this->answers['phone'] ?? '';

        return rescue(fn (): string => new PhoneNumber($phone)->formatInternational(), $phone, report: false);
    }
};
?>

<form wire:submit="nextStep" class="flex flex-col gap-6">
    <flux:card size="sm" variant="soft" body="inset">
        <flux:card.header>
            <flux:card.heading>{{ __('auth.register.steps.identity.label') }}</flux:card.heading>
            <flux:card.actions class="mt-0 mr-0">
                <flux:link as="button" class="text-sm" wire:click="edit('first_name')">
                    {{ __('wizard.edit') }}
                </flux:link>
            </flux:card.actions>
        </flux:card.header>

        <flux:card.body>
            <dl class="[&>dd:not(:last-child)]:mb-2 sm:[&>dd]:mb-0 grid grid-cols-1 gap-x-6 gap-y-1 text-sm sm:grid-cols-[8rem_1fr] sm:gap-y-3">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.name.label') }}</dt>
                <dd class="font-medium">
                    {{ $this->answers['first_name'] ?? '' }} {{ $this->answers['last_name'] ?? '' }}
                </dd>

                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.email.label') }}</dt>
                <dd class="font-medium">{{ $this->answers['email'] ?? '' }}</dd>
            </dl>
        </flux:card.body>
    </flux:card>

    <flux:card size="sm" variant="soft" body="inset">
        <flux:card.header>
            <flux:card.heading>{{ __('auth.register.steps.details.label') }}</flux:card.heading>
            <flux:card.actions class="mt-0 mr-0">
                <flux:link as="button" class="text-sm" wire:click="edit('gender')">{{ __('wizard.edit') }}</flux:link>
            </flux:card.actions>
        </flux:card.header>

        <flux:card.body>
            <dl class="[&>dd:not(:last-child)]:mb-2 sm:[&>dd]:mb-0 grid grid-cols-1 gap-x-6 gap-y-1 text-sm sm:grid-cols-[8rem_1fr] sm:gap-y-3">
                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.class.label') }}</dt>
                <dd class="font-medium">
                    {{ ClassCourse::tryFrom($this->answers['class_course'] ?? '')?->getLabel() }} {{ $this->answers['class_year'] ?? '' }}
                </dd>

                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.gender.label') }}</dt>
                <dd class="font-medium">{{ Gender::tryFrom($this->answers['gender'] ?? '')?->getLabel() }}</dd>

                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.birth-date.label') }}</dt>
                <dd class="font-medium">
                    {{ filled($this->answers['birth_date'] ?? null) ? Date::parse($this->answers['birth_date'])->isoFormat('LL') : '' }}
                </dd>

                <dt class="text-zinc-500 dark:text-zinc-400">{{ __('fields.phone.label') }}</dt>
                <dd class="font-medium">{{ $this->phone }}</dd>
            </dl>
        </flux:card.body>
    </flux:card>

    @include('partials.step-actions', ['lastStepLabel' => __('auth.register.action')])
</form>
