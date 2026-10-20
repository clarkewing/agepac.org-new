<?php

use App\Actions\Fortify\ProfileValidationRules;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use Livewire\Attributes\Computed;
use Spatie\LivewireWizard\Components\StepComponent;

/**
 * Profile details step, shared by registration and account recovery. The
 * class is only asked for when the wizard says it is missing.
 */
new class extends StepComponent
{
    use ProfileValidationRules;

    public bool $requiresClass = true;

    public string $class_course = '';

    public string $class_year = '';

    public string $gender = '';

    public string $birth_date = '';

    public string $phone = '';

    public function save(): void
    {
        $this->validate([
            ...($this->requiresClass ? $this->classRules() : []),
            ...$this->detailsRules(),
        ]);

        $this->nextStep();
    }
};
?>

<form wire:submit="save" class="flex flex-col gap-6">
    @if ($requiresClass)
        <!-- Class Course -->
        <flux:select
            wire:model="class_course"
            :label="__('fields.class-course.label')"
            required
            autofocus
            :placeholder="__('fields.class-course.placeholder')"
        >
            @foreach (ClassCourse::cases() as $course)
                <flux:select.option :value="$course->value" :label="$course->getLabel()" />
            @endforeach
        </flux:select>

        <!-- Class Year -->
        {{-- A two-digit year expands the way card expiry inputs do: up to ten years ahead means this century. --}}
        <flux:input
            wire:model="class_year"
            :label="__('fields.class-year.label')"
            type="text"
            inputmode="numeric"
            maxlength="4"
            required
            placeholder="YYYY"
            x-on:change="
                if (/^\d{2}$/.test($el.value)) {
                    $el.value =
                        (Number($el.value) <= (new Date().getFullYear() + 10) % 100 ? 2000 : 1900) + Number($el.value);
                    $el.dispatchEvent(new Event('input', { bubbles: true }));
                }
            "
        />
    @endif

    <!-- Gender -->
    <flux:select
        wire:model="gender"
        :label="__('fields.gender.label')"
        required
        :autofocus="! $requiresClass"
        :placeholder="__('fields.gender.placeholder')"
    >
        @foreach (Gender::cases() as $genderOption)
            <flux:select.option :value="$genderOption->value" :label="$genderOption->getLabel()" />
        @endforeach
    </flux:select>

    <!-- Birth Date -->
    <flux:date-picker
        wire:model="birth_date"
        :label="__('fields.birth-date.label')"
        selectable-header
        :open-to="now()->subYears(20)->toDateString()"
    >
        <x-slot name="trigger">
            <flux:date-picker.input required autocomplete="bday" :invalid="$errors->has('birth_date')" />
        </x-slot>
    </flux:date-picker>

    <!-- Phone -->
    <flux:phone
        wire:model="phone"
        :label="__('fields.phone.label')"
        required
        country="fr"
        :country-order="[
            'fr', 're', 'gp', 'mq', 'gf', 'yt', 'nc', 'pf',
            'be', 'ch', 'lu', 'mc',
            'ca', 'de', 'es', 'it',
        ]"
    />

    @include('partials.step-actions')
</form>
