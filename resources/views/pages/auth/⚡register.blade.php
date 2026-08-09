<?php

use App\Actions\MakeUsername;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Laravel\Head\Facades\Head;
use libphonenumber\NumberParseException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;

new #[Layout('layouts::auth')] class extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $class_course = '';

    public string $class_year = '';

    public string $gender = '';

    public string $birth_date = '';

    public string $phone = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(MakeUsername $makeUsername): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'class_course' => ['required', Rule::enum(ClassCourse::class)],
            'class_year' => ['required', Rule::date()->format('Y')],
            'gender' => ['required', Rule::enum(Gender::class)],
            'birth_date' => ['required', Rule::date()->format('Y-m-d')->after('150 years ago')->beforeOrEqual('15 years ago')],
            'phone' => ['required', (new Phone)->international()->country('FR')],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $validated['username'] = $makeUsername($validated['first_name'], $validated['last_name']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function updatedPhone(): void
    {
        try {
            $this->phone = new PhoneNumber($this->phone, 'FR')->formatInternational();
        } catch (NumberParseException) {
        }
    }

    public function rendering(): void
    {
        Head::title(__('auth.register.title'));
    }
};
?>


<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.register.heading')" :description="__('auth.register.description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
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

        <!-- Gender -->
        <flux:select
            wire:model="gender"
            :label="__('fields.gender.label')"
            required
            :placeholder="__('fields.gender.placeholder')"
        >
            @foreach(Gender::options() as $value => $label)
                <flux:select.option :$value :$label />
            @endforeach
        </flux:select>

        <!-- Birth Date -->
        <flux:date-picker
            wire:model="birth_date"
            :label="__('fields.birth-date.label')"
            required
        >
            <x-slot name="trigger">
                <flux:date-picker.input autocomplete="bday" />
            </x-slot>
        </flux:date-picker>

        <!-- Class Course -->
        <flux:select
            wire:model="class_course"
            :label="__('fields.class-course.label')"
            required
            :placeholder="__('fields.class-course.placeholder')"
        >
            @foreach(ClassCourse::options() as $option)
                <flux:select.option :value="$option" :label="$option" />
            @endforeach
        </flux:select>

        <!-- Class Year -->
        <flux:input
            wire:model="class_year"
            :label="__('fields.class-year.label')"
            type="text"
            required
            placeholder="YYYY"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('fields.email.label')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Phone -->
        <flux:input
            wire:model.blur="phone"
            :loading="true"
            :label="__('fields.phone.label')"
            type="tel"
            required
            autocomplete="tel"
            :placeholder="__('fields.phone.placeholder')"
        >
            <x-slot name="iconTrailing">
                <flux:input-tooltip>
                    <p>{{ __('fields.phone.tooltip') }}</p>
                </flux:input-tooltip>
            </x-slot>
        </flux:input>

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('fields.password.label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('fields.password.placeholder')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('fields.password-confirmation.label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('fields.password-confirmation.placeholder')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('auth.register.action') }}
            </flux:button>
        </div>
    </form>

    <flux:subheading class="text-center text-zinc-600 dark:text-zinc-400">
        <span>{{ __('auth.register.login-prompt') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('auth.register.login-link') }}</flux:link>
    </flux:subheading>
</div>
