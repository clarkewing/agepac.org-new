<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\SubscriptionItemObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\UserObserver;
use App\Services\Stripe\NightwatchCurlClient;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;
use Laravel\Nightwatch\Core as NightwatchCore;
use Stripe\ApiRequestor as StripeApiRequestor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Number::useLocale(config('cashier.currency_locale'));
        Number::useCurrency(config('cashier.currency'));

        User::observe(UserObserver::class);
        Subscription::observe(SubscriptionObserver::class);
        SubscriptionItem::observe(SubscriptionItemObserver::class);

        Blade::anonymousComponentPath(resource_path('views/public/components'), 'public');

        StripeApiRequestor::setHttpClient(
            new NightwatchCurlClient(app(NightwatchCore::class))
        );
    }
}
