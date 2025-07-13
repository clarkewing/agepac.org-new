@use(App\Enums\ClassCourse)
@use(App\Enums\Gender)

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- First Name -->
        <flux:input
            wire:model="first_name"
            :label="__('First Name')"
            type="text"
            required
            autofocus
            autocomplete="given-name"
            :placeholder="__('First name')"
        />

        <!-- Last Name -->
        <flux:input
            wire:model="last_name"
            :label="__('Last Name')"
            type="text"
            required
            autocomplete="family-name"
            :placeholder="__('Last name')"
        />

        <!-- Gender -->
        <flux:select
            wire:model="gender"
            :label="__('Gender')"
            required
            :placeholder="__('Select gender…')"
        >
            @foreach(Gender::options() as $value => $label)
                <flux:select.option :$value :$label />
            @endforeach
        </flux:select>

        <!-- Birth Date -->
        <flux:date-picker
            wire:model="birth_date"
            :label="__('Birth Date')"
            required
        >
            <x-slot name="trigger">
                <flux:date-picker.input autocomplete="bday" />
            </x-slot>
        </flux:date-picker>

        <!-- Class Course -->
        <flux:select
            wire:model="class_course"
            :label="__('Class Course')"
            required
            :placeholder="__('Choose course…')"
        >
            @foreach(ClassCourse::options() as $option)
                <flux:select.option :value="$option" :label="$option" />
            @endforeach
        </flux:select>

        <!-- Class Year -->
        <flux:input
            wire:model="class_year"
            :label="__('Class Year')"
            type="text"
            required
            :placeholder="__('YYYY')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Phone -->
        <flux:input
            wire:model.blur="phone"
            :loading="true"
            :label="__('Phone')"
            type="tel"
            required
            autocomplete="tel"
            :placeholder="__('Phone number')"
        >
            <x-slot name="iconTrailing">
                <flux:tooltip position="bottom" toggleable>
                    <flux:button icon="information-circle" size="sm" variant="subtle" inset="left right" />
                    <flux:tooltip.content class="max-w-56">
                        <p>{{ __('International phone numbers are also accepted') }}</p>
                    </flux:tooltip.content>
                </flux:tooltip>
            </x-slot>
        </flux:input>

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
