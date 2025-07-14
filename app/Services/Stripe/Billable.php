<?php

namespace App\Services\Stripe;

use Laravel\Cashier\Billable as CashierBillable;

trait Billable
{
    use CashierBillable;

    public function stripePhone(): ?string
    {
        return $this->phone?->formatInternational();
    }

    public function stripeMetadata(): ?array
    {
        return [
            'class' => $this->class,
        ];
    }
}
