<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used within the Filament admin panel:
    | resource labels, section headings, table columns, and actions.
    | Canonical field labels live in fields.php.
    |
    */

    'users' => [
        'label' => 'user',
        'sections' => [
            'identity' => 'Identity',
            'account' => 'Account',
            'class' => 'Class',
            'history' => 'History',
        ],
        'columns' => [
            'class' => 'Class',
        ],
        'filters' => [
            'approved' => 'Approved',
        ],
        'timestamps' => [
            'created_at' => 'Registered',
            'updated_at' => 'Updated',
            'email_verified_at' => 'Email verified',
            'approved_at' => 'Approved',
        ],
        'actions' => [
            'approve' => [
                'label' => 'Approve',
                'success' => 'User approved.',
                'modal' => [
                    'heading' => 'Approve this user?',
                    'description' => 'The user will gain access to the members area.',
                ],
            ],
            'assign-role' => [
                'label' => 'Assign role',
                'placeholder' => 'None',
                'success' => 'Role updated.',
                'unapproved' => 'The user must be approved before they can be given a role.',
            ],
            'send-password-reset' => [
                'label' => 'Reset password',
                'success' => 'Password reset link sent.',
                'modal' => [
                    'heading' => 'Send a password reset link?',
                    'description' => 'An email with a password reset link will be sent to :email.',
                ],
            ],
            'resend-verification' => [
                'label' => 'Resend',
                'success' => 'Verification email sent.',
                'modal' => [
                    'heading' => 'Resend the verification email?',
                    'description' => 'A new verification link will be sent to :email.',
                ],
            ],
        ],
    ],

];
