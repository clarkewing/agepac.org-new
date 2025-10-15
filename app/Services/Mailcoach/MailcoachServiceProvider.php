<?php

namespace App\Services\Mailcoach;

use Illuminate\Support\ServiceProvider;

class MailcoachServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MailcoachApi::class, fn () => new MailcoachApi);
    }
}
