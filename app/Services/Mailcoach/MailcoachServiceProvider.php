<?php

namespace App\Services\Mailcoach;

use App\Services\Mailcoach\Testing\Fakes\MailcoachApiFake;
use Illuminate\Support\ServiceProvider;

class MailcoachServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MailcoachApi::class, function (): MailcoachApi {
            // Outside production, leaving Mailcoach unconfigured swaps in the
            // in-memory fake, so local sign-ups never reach the live list.
            if (! $this->app->isProduction() && blank(config('services.mailcoach.url'))) {
                return new MailcoachApiFake;
            }

            return new MailcoachApi;
        });
    }
}
