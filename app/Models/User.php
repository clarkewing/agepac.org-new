<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\Casts\E164PhoneNumberCast;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;

    public function getConnectionName()
    {
        return config('app.users_connection');
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'class_course',
        'class_year',
        'gender',
        'birthdate',
        'phone',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'approved_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthdate' => 'date:Y-m-d',
            'phone' => E164PhoneNumberCast::class.':FR',
            'flight_hours' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::nameCase($value),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Str::nameCase($value),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => implode(' ', [
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
