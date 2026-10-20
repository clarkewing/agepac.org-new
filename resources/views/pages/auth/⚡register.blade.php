<?php

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Spatie\LivewireWizard\Components\WizardComponent;

new #[Layout('layouts::auth')]
class extends WizardComponent
{
    /**
     * The step to come back to once the one being shown is saved, set when
     * the review step sends the member off to change an answer.
     */
    public ?string $returnTo = null;

    /**
     * @return array<int, string>
     */
    public function steps(): array
    {
        return [
            'auth.steps.identity',
            'auth.steps.details',
            'auth.steps.password',
            'auth.steps.review',
        ];
    }

    #[On('editField')]
    public function editField(string $field, array $currentStepState = []): void
    {
        $this->resetErrorBag();

        $this->returnTo = $this->currentStepName;

        $this->showStep($this->stepFor($field), $currentStepState);
    }

    #[On('nextStep')]
    public function nextStep(array $currentStepState): void
    {
        if ($this->currentStepName === $this->stepNames()->last()) {
            // Store the last step's answers: showStep() hydrates the state it is handed.
            $this->showStep($this->currentStepName, $currentStepState);

            $this->complete();

            return;
        }

        if (is_null($returnTo = $this->returnTo)) {
            parent::nextStep($currentStepState);

            return;
        }

        $this->returnTo = null;

        $this->showStep($returnTo, $currentStepState);
    }

    #[On('previousStep')]
    public function previousStep(array $currentStepState): void
    {
        $this->returnTo = null;

        parent::previousStep($currentStepState);
    }

    protected function complete(): void
    {
        event(new Registered($user = app(CreatesNewUsers::class)->create($this->answers())));

        Auth::login($user);

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * The current step's short name, which keys its heading and description.
     */
    #[Computed]
    public function step(): string
    {
        return Str::afterLast($this->currentStepName, '.');
    }

    #[Computed]
    public function position(): int
    {
        return $this->stepNames()->search($this->currentStepName) + 1;
    }

    protected function stepFor(string $field): string
    {
        return $this->stepNames()->first(
            fn (string $step): bool => array_key_exists($field, Arr::get($this->allStepState, $step) ?? []),
            $this->stepNames()->first(),
        );
    }

    protected function answers(): array
    {
        return $this->stepNames()
            ->flatMap(fn (string $step): array => Arr::get($this->allStepState, $step) ?? [])
            ->all();
    }

    public function rendering(): void
    {
        Head::title(__('auth.register.title'));
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('auth.register.steps.'.$this->step.'.heading')"
        :description="__('auth.register.steps.'.$this->step.'.description')"
    />

    <flux:text size="sm" class="text-center">
        {{ __('wizard.progress', ['current' => $this->position, 'total' => $this->stepNames()->count()]) }}
    </flux:text>

    @if ($errors->any())
        <flux:callout variant="danger" icon="x-circle">
            @foreach ($errors->keys() as $field)
                <flux:callout.text>
                    {{ $errors->first($field) }}
                    <flux:link as="button" wire:click="editField('{{ $field }}')">{{ __('wizard.fix') }}</flux:link>
                </flux:callout.text>
            @endforeach
        </flux:callout>
    @endif

    @livewire($currentStepName, $currentStepState, key($currentStepName))

    @if ($this->position === 1)
        <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
            <span>{{ __('auth.register.login-prompt') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('auth.register.login-link') }}</flux:link>
        </flux:subheading>
    @endif
</div>
