<?php

namespace App\Providers;

use App\Observers\SubscriptionItemObserver as CashierSubscriptionItemObserver;
use App\Observers\SubscriptionObserver as CashierSubscriptionObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Subscription as CashierSubscription;
use Laravel\Cashier\SubscriptionItem as CashierSubscriptionItem;
use Laravel\Head\Enums\ImageType;
use Laravel\Head\Enums\RobotsRule;
use Laravel\Head\Facades\Head;
use Laravel\Head\HeadBuilder;

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
        $this->bootHead();
        $this->bootModelObservers();

        Number::useLocale(config('cashier.currency_locale'));
        Number::useCurrency(config('cashier.currency'));

        Blade::anonymousComponentPath(resource_path('views/public/components'), 'public');
    }

    public function bootHead(): void
    {
        $siteName = config('app.name');

        Head::defaults(fn (HeadBuilder $head) => $head
            ->title($siteName, suffix: " · {$siteName}")
            ->canonical()
            ->robots([RobotsRule::NoIndex, RobotsRule::Follow])
            ->viewport('width=device-width, initial-scale=1')
            ->favicon('/favicon.svg', type: ImageType::Svg)
            ->icon('/favicon.ico', sizes: 'any')
            ->appleTouchIcon('/apple-touch-icon.png'));
    }

    /**
     * Register observers for vendor models, which cannot carry the
     * #[ObservedBy] attribute; app models declare theirs on the class.
     */
    public function bootModelObservers(): void
    {
        CashierSubscription::observe(CashierSubscriptionObserver::class);
        CashierSubscriptionItem::observe(CashierSubscriptionItemObserver::class);
    }
}
