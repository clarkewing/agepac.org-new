<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\MacroServiceProvider;
use App\Services\Mailcoach\MailcoachServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    MacroServiceProvider::class,
    MailcoachServiceProvider::class,
];
