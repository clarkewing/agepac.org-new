<?php

namespace App\Services\Mailcoach\Facades;

use App\Services\Mailcoach\MailcoachApi;
use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;
use Illuminate\Support\Facades\Facade;

class Mailcoach extends Facade
{
    public static function fake(): MailcoachApiFake
    {
        static::swap($fake = new MailcoachApiFake);

        return $fake;
    }

    public static function isFake(): bool
    {
        return static::getFacadeRoot() instanceof MailcoachApiFake;
    }

    protected static function getFacadeAccessor(): string
    {
        return MailcoachApi::class;
    }
}
