<?php

namespace App\Livewire\Settings;

use App\Actions\CreateSubscriptionCheckout;
use App\Enums\Products\Membership as MembershipProduct;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Checkout;
use Livewire\Component;

class CreateMembershipForm extends Component
{
    public ?string $selectedMembership = null;

    public function checkout(CreateSubscriptionCheckout $checkout): Checkout
    {
        $this->validate();

        $selectedMembership = MembershipProduct::from($this->selectedMembership);

        return $checkout(
            auth()->user(),
            $selectedMembership->stripePrice()->id,
            route('settings.membership'),
            route('settings.membership'),
            type: 'membership',
            sessionOptions: [
                'payment_method_data' => ['allow_redisplay' => 'always'], // Ensure payment method can be redisplayed
                'subscription_data' => ['description' => $selectedMembership->description()],
            ],
        );
    }

    protected function rules(): array
    {
        return [
            'selectedMembership' => [
                'required',
                Rule::enum(MembershipProduct::class),
            ],
        ];
    }
}
