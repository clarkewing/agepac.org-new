<?php

use App\Actions\Fortify\PasswordValidationRules;
use App\Actions\Fortify\ProfileValidationRules;
use App\Models\User;
use App\Services\Auth\AccountRecovery;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Head\Facades\Head;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Spatie\LivewireWizard\Components\WizardComponent;

new #[Layout('layouts::auth')] class extends WizardComponent
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public function mount(): void
    {
        if (! $this->verified || ! $this->user->requiresMigration()) {
            $this->redirectRoute('recovery', navigate: true);
        }
    }

    /**
     * @return array<int, string>
     */
    public function steps(): array
    {
        return [
            'auth.steps.details',
            'auth.steps.password',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function initialState(): array
    {
        if (! $this->verified) {
            return [];
        }

        return [
            'auth.steps.details' => [
                'requiresClass' => $this->mustSetClass,
                'class_course' => $this->user->class_course?->value ?? '',
                'class_year' => (string) ($this->user->class_year ?? ''),
                'gender' => $this->user->gender ?? '',
                'birth_date' => $this->user->birth_date?->format('Y-m-d') ?? '',
                'phone' => $this->user->phone?->formatE164() ?? '',
            ],
        ];
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

        parent::nextStep($currentStepState);
    }

    #[Computed]
    public function user(): ?User
    {
        return AccountRecovery::user();
    }

    #[Computed]
    public function verified(): bool
    {
        return AccountRecovery::isVerified();
    }

    #[Computed]
    public function mustSetClass(): bool
    {
        return is_null($this->user->class_course) || is_null($this->user->class_year);
    }

    #[Computed]
    public function step(): string
    {
        return Str::afterLast($this->currentStepName, '.');
    }

    protected function answers(): array
    {
        return $this->stepNames()
            ->flatMap(fn (string $step): array => Arr::get($this->allStepState, $step) ?? [])
            ->all();
    }

    protected function complete(): void
    {
        abort_unless($this->verified, 403);

        $validated = Validator::make($this->answers(), [
            ...($this->mustSetClass ? $this->classRules() : []),
            ...$this->detailsRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        AccountRecovery::complete($this->user, $validated);

        $this->redirect(app(LoginResponse::class)->toResponse(request())->getTargetUrl());
    }

    public function rendering(): void
    {
        Head::title(__('auth.recovery.title'));
    }
};
?>

<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('auth.recovery.heading.migration')"
        :description="__('auth.recovery.steps.'.$this->step.'.description', ['name' => $this->user->first_name])"
    />

    @if ($errors->any())
        <flux:callout variant="danger" icon="x-circle">
            @foreach ($errors->all() as $message)
                <flux:callout.text>{{ $message }}</flux:callout.text>
            @endforeach
        </flux:callout>
    @endif

    @livewire($currentStepName, $currentStepState, key($currentStepName))
</div>
