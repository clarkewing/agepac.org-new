@use(App\Enums\ClassCourse)
@use(App\Enums\Gender)

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
