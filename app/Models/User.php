<?php

namespace App\Models;

use App\Models\Concerns\Approvable;
use App\Models\Concerns\HasRole;
use App\Observers\UserObserver;
use App\Services\Stripe\Billable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

#[Fillable([
    'first_name',
    'last_name',
    'username',
    'email',
    'password',
    'class_course',
    'class_year',
    'gender',
    'birth_date',
    'phone',
    'avatar_path',
])]
#[Hidden([
    'password',
    'remember_token',
    'email_verified_at',
    'approved_at',
])]
#[ObservedBy(UserObserver::class)]
#[RouteKey('username')]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use Approvable;
    use Billable;
    use HasFactory;
    use HasRole;
    use Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date:Y-m-d',
            'phone' => E164PhoneNumberCast::class.':FR',
            'flight_hours' => 'integer',
        ];
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::nameCase($value),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value): string => Str::nameCase($value),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => implode(' ', [
                $attributes['first_name'],
                $attributes['last_name'],
            ]),
        );
    }

    protected function class(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => implode(' ', [
                $attributes['class_course'],
                $attributes['class_year'],
            ]),
        );
    }

    public function initials(): string
    {
        return Str::substr($this->first_name, 0, 1).Str::substr($this->last_name, 0, 1);
    }
}
