<?php

return [
    'title' => 'Settings',
    'description' => 'Manage your profile and account settings',

    'appearance' => [
        'heading' => 'Appearance',
        'subheading' => 'Update the appearance settings for your account',
        'theme' => [
            'label' => 'Theme',
            'options' => [
                'light' => 'Light',
                'dark' => 'Dark',
                'system' => 'System',
            ],
        ],
    ],

    'profile' => [
        'heading' => 'Profile',
        'subheading' => 'Update your name and email address',
        'action' => 'Save',
        'email-verification' => [
            'prompt' => 'Your email address is unverified.',
            'action' => 'Click here to re-send the verification email.',
            'status' => [
                'verification-link-sent' => 'A new verification link has been sent to your email address.',
            ],
        ],
        'delete-account' => [
            'heading' => 'Delete account',
            'subheading' => 'Permanently delete your account and all of its resources',
            'prompt' => 'Are you sure you want to delete your account?',
            'action' => 'Delete account',
            'confirmation-modal' => [
                'heading' => 'Are you sure you want to delete your account?',
                'subheading' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
                'cancel-action' => 'Cancel',
            ],
        ],
    ],

    'password' => [
        'heading' => 'Update password',
        'subheading' => 'Ensure your account is using a long, random password to stay secure',
        'action' => 'Save',
    ],

    'membership' => [
        'heading' => 'Membership',
        'subheading' => 'Manage your AGEPAC membership',
        'payment-action' => 'Continue to payment',
        'manage-action' => 'Manage membership',
        'product' => [
            'label' => 'Select your membership option',
        ],
        'callouts' => [
            'subscription-active' => [
                'heading' => 'Membership active',
                'text' => 'You’re currently subscribed to the :plan plan. To manage or cancel your membership, click the “Manage membership” button below.',
            ],
            'subscription-trial' => [
                'heading' => 'Trial membership',
                'text' => 'You’re currently on a trial membership that will end on :date. To ensure your membership continues beyond the trial period, click the “Manage membership” button below.',
            ],
            'subscription-ended' => [
                'heading' => 'Membership ended',
                'text' => 'Your membership has ended. To start a new membership, use the form below.',
            ],
            'subscription-inactive' => [
                'heading' => 'Inactive membership',
                'text' => 'You do not have an active AGEPAC membership. To start a new membership, use the form below.',
            ],
            'subscription-past-due' => [
                'heading' => 'Payment past due',
                'text' => 'The last payment for your membership failed.',
                'action' => 'Complete payment',
            ],
            'subscription-incomplete' => [
                'heading' => 'Incomplete payment',
                'text' => 'Your payment could not be completed and requires further action.',
                'action' => 'Complete payment',
            ],
            'checkout-completed' => [
                'heading' => 'Checkout completed',
                'text' => 'Your payment is being processed, it might be a few minutes before your new membership appears.',
            ],
            'checkout-interrupted' => [
                'heading' => 'Checkout interrupted',
                'text' => 'It looks like the checkout flow was interrupted. When you’re ready, please try again.',
            ],
            'update-pending' => [
                'heading' => 'Pending update',
                'text' => 'Your membership has a pending update that will be applied on your next billing cycle. To manage your membership, click the “Manage membership” button below.',
            ],
            'no-auto-renew' => [
                'heading' => 'Auto-renew deactivated',
                'text' => 'You will no longer be an AGEPAC member on :date. You can resume your membership by clicking the button below.',
                'action' => 'Resume membership',
            ],
            'sepa-enticement' => [
                'heading' => 'Choosing SEPA Direct Debit helps us keep membership costs low',
                'text' => 'It’s simpler for renewals and saves on fees — so more of your contribution goes where it matters.',
                'action' => 'Learn more',
            ],
        ],
    ],
];
