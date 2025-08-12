<div class="mt-4 flex flex-col gap-6">
    <flux:text class="text-center">
        {{ __('auth.verify-email.heading') }}
    </flux:text>

    @if (session('status') == 'verification-link-sent')
        <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
            {{ __('auth.verify-email.status.verification-link-sent') }}
        </flux:text>
    @endif

    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button wire:click="sendVerification" variant="primary" class="w-full">
            {{ __('auth.verify-email.action') }}
        </flux:button>

        <flux:link class="text-sm cursor-pointer" wire:click="logout">
            {{ __('auth.verify-email.logout-link') }}
        </flux:link>
    </div>
</div>
