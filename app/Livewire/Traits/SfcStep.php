<?php

namespace App\Livewire\Traits;

use Spatie\LivewireWizard\Support\State;

/**
 * Lets a single-file component extend Spatie's StepComponent, which looks
 * itself up by class. See SfcWizard.
 *
 * @todo Delete once spatie/laravel-livewire-wizard#159 ships.
 */
trait SfcStep
{
    public function hasPreviousStep(): bool
    {
        return ! empty($this->allStepNames) && $this->allStepNames[0] !== $this->stepName();
    }

    public function hasNextStep(): bool
    {
        return end($this->allStepNames) !== $this->stepName();
    }

    public function state(): State
    {
        return (new $this->stateClassName)
            ->setAllState([...$this->allStepsState, $this->stepName() => $this->all()])
            ->setCurrentStepName($this->stepName());
    }

    /**
     * The name Livewire gave this step; a step constructed by hand falls
     * back to its class.
     */
    protected function stepName(): string
    {
        return $this->getName() ?? app('livewire.finder')->normalizeName(static::class);
    }
}
