<?php

use App\Actions\CreateSubscriptionCheckout;
use App\Enums\Products\Membership;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public ?string $selectedMembership = null;

    public function checkout(CreateSubscriptionCheckout $checkout): void
    {
        $this->validate();

        $selectedMembership = Membership::from($this->selectedMembership);

        $session = $checkout(
            auth()->user(),
            $selectedMembership->stripePrice()->id,
            route('settings.membership'),
            route('settings.membership'),
            type: 'membership',
            sessionOptions: [
                'payment_method_data' => ['allow_redisplay' => 'always'], // Ensure payment method can be redisplayed
            ],
        );

        $this->redirect($session->url);
    }

    protected function rules(): array
    {
        return [
            'selectedMembership' => [
                'required',
                Rule::enum(Membership::class),
            ],
        ];
    }
};
?>

<div class="space-y-4">
    <flux:radio.group
        variant="cards"
        class="flex-col"
        :label="__('settings.membership.product.label')"
        wire:model="selectedMembership"
    >
        @foreach (Membership::cases() as $membershipOption)
            <flux:radio :value="$membershipOption->value">
                <flux:radio.indicator />
                <div class="flex-1">
                    <div class="flex gap-1 max-sm:flex-col md:items-center md:gap-2">
                        <flux:heading class="leading-4">{{ $membershipOption->label() }}</flux:heading>
                        <flux:text>{{ Number::currency($membershipOption->stripePrice()->unit_amount / 100) }}</flux:text>
                    </div>
                    <flux:text size="sm" class="mt-2">{{ $membershipOption->description() }}</flux:text>
                </div>
            </flux:radio>
        @endforeach
    </flux:radio.group>

    <flux:button variant="primary" class="max-sm:w-full" wire:click="checkout">
        {{ __('settings.membership.payment-action') }}
    </flux:button>
</div>
