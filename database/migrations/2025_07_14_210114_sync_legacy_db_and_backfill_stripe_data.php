<?php

use ClarkeWing\LegacySync\Enums\SyncDirection;
use ClarkeWing\LegacySync\Facades\LegacySync;
use Illuminate\Database\Migrations\Migration;
use Laravel\Cashier\Cashier;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        LegacySync::syncAll(SyncDirection::LegacyToNew);

        $this->migrateSubscriptionTypes();

        $this->backfillStripeProductIds();
    }

    protected function migrateSubscriptionTypes(): void
    {
        DB::table('subscriptions')
            ->update(['type' => 'membership']);
    }

    protected function backfillStripeProductIds(): void
    {
        $priceIds = DB::table('subscription_items')
            ->select('stripe_price')
            ->distinct()
            ->pluck('stripe_price');

        foreach ($priceIds as $priceId) {
            try {
                $price = Cashier::stripe()->prices->retrieve($priceId);

                DB::table('subscription_items')
                    ->where('stripe_price', $priceId)
                    ->update(['stripe_product' => $price->product]);

            } catch (\Throwable $e) {
                report($e);
                logger()->warning("Could not retrieve Stripe product for price ID: {$priceId}");
            }
        }
    }
};
