<?php

use App\Providers\AppServiceProvider;
use App\Providers\MacroServiceProvider;
use App\Services\Mailcoach\MailcoachServiceProvider;

return [
    AppServiceProvider::class,
    MacroServiceProvider::class,
    MailcoachServiceProvider::class,
];
