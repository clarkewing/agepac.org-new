<?php

use App\Actions\Fortify\ProfileValidationRules;
use App\Livewire\Traits\SfcStep;
use Illuminate\Support\Str;
use Spatie\LivewireWizard\Components\StepComponent;

new class extends StepComponent
{
    use ProfileValidationRules;

    // TODO: Drop once spatie/laravel-livewire-wizard#159 ships.
    use SfcStep;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public function save(): void
    {
        $this->email = Str::lower(trim($this->email));

        $this->validate($this->identityRules());

        $this->nextStep();
    }
};
?>

<form wire:submit="save" class="flex flex-col gap-6">
    <!-- First Name -->
    <flux:input
        wire:model="first_name"
        :label="__('fields.first-name.label')"
        type="text"
        required
        autofocus
        autocomplete="given-name"
        :placeholder="__('fields.first-name.placeholder')"
    />

    <!-- Last Name -->
    <flux:input
        wire:model="last_name"
        :label="__('fields.last-name.label')"
        type="text"
        required
        autocomplete="family-name"
        :placeholder="__('fields.last-name.placeholder')"
    />

    <!-- Email Address -->
    <flux:input
        wire:model="email"
        :label="__('fields.email.label')"
        type="email"
        required
        autocomplete="email"
        placeholder="jp.celton@example.com"
    />

    @include('partials.step-actions')
</form>
