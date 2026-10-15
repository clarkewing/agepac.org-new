<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Exceptions\ComponentNotFoundException;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\LivewireWizard\Exceptions\InvalidStepComponent;
use Spatie\LivewireWizard\Exceptions\NoStepsReturned;

/**
 * Lets a single-file component extend Spatie's WizardComponent. The package
 * identifies steps and the wizard by class, which compiled components do
 * not have, so steps are resolved by the name Livewire gave them and the
 * wizard renders its own template. Mirrors spatie/laravel-livewire-wizard#159.
 *
 * @todo Delete this trait, SfcStep and their uses once that PR ships.
 */
trait SfcWizard
{
    /**
     * @return Collection<int, string>
     */
    public function stepNames(): Collection
    {
        $steps = collect($this->steps())
            ->map(function (string $step): string {
                try {
                    [$name, $class] = app('livewire.factory')->resolveComponentNameAndClass($step);
                } catch (ComponentNotFoundException) {
                    throw class_exists($step)
                        ? InvalidStepComponent::doesNotExtendStepComponent(static::class, $step)
                        : InvalidStepComponent::notRegisteredWithLivewire(static::class, $step);
                }

                if (! is_a($class, StepComponent::class, true)) {
                    throw InvalidStepComponent::doesNotExtendStepComponent(static::class, $step);
                }

                return $name;
            });

        if ($steps->isEmpty()) {
            throw NoStepsReturned::make(static::class);
        }

        return $steps;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCurrentStepState(?string $step = null): array
    {
        return [
            ...parent::getCurrentStepState($step),
            'wizardClassName' => $this->getName(),
        ];
    }

    public function render(): View
    {
        $currentStepState = $this->getCurrentStepState();

        return $this->getProvidedView()->with(compact('currentStepState'));
    }
}
