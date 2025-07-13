<?php

namespace App\Livewire\Auth;

use App\Actions\MakeUsername;
use App\Enums\ClassCourse;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use libphonenumber\NumberParseException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Propaganistas\LaravelPhone\PhoneNumber;
use Propaganistas\LaravelPhone\Rules\Phone;

#[Layout('components.layouts.auth')]
class Register extends Component
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
}
