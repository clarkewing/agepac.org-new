<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\MacroServiceProvider;
use App\Services\Mailcoach\MailcoachServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    AdminPanelProvider::class,
    FortifyServiceProvider::class,
    MacroServiceProvider::class,
    MailcoachServiceProvider::class,
];
