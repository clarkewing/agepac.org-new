<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\SubscriptionItemObserver as CashierSubscriptionItemObserver;
use App\Observers\SubscriptionObserver as CashierSubscriptionObserver;
use App\Observers\UserObserver;
use App\Services\Stripe\NightwatchCurlClient;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Subscription as CashierSubscription;
use Laravel\Cashier\SubscriptionItem as CashierSubscriptionItem;
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
        $this->bootModelObservers();

        Number::useLocale(config('cashier.currency_locale'));
        Number::useCurrency(config('cashier.currency'));

        Blade::anonymousComponentPath(resource_path('views/public/components'), 'public');

        StripeApiRequestor::setHttpClient(
            new NightwatchCurlClient(app(NightwatchCore::class))
        );
    }

    public function bootModelObservers(): void
    {
        User::observe(UserObserver::class);
        CashierSubscription::observe(CashierSubscriptionObserver::class);
        CashierSubscriptionItem::observe(CashierSubscriptionItemObserver::class);
    }
}
