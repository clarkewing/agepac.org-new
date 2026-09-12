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

    'pages' => [
        'label' => 'page',
        'create' => [
            'title' => 'Create a page',
            'create_another' => 'Create & add another',
        ],
        'columns' => [
            'scheduled' => 'Scheduled',
            'unpublished' => 'Unpublished',
        ],
        'sections' => [
            'content' => 'Content',
            'settings' => 'Settings',
        ],
        'actions' => [
            'now' => 'Now (UTC)',
            'clear' => 'Clear',
        ],
        'warnings' => [
            'public_attachments' => [
                'title' => 'Attachments on a public page',
                'description' => 'Attachments are only served to approved members. On a public page, they will appear broken to visitors. The affected attachments are listed below.',
            ],
            'converted' => 'Switching the format converts the body and can result in data loss.',
        ],
        'help' => [
            'restricted' => 'A public page is viewable by anyone, even without an account.',
            'published_at' => 'The date and time at which this page becomes widely viewable. Leave empty to keep the page unpublished.',
        ],
        'cheatsheet' => [
            'heading' => 'Writing guide',
            'syntax' => [
                'heading' => 'Markdown syntax',
                'rows' => [
                    ['label' => 'Heading', 'example' => '## Heading'],
                    ['label' => 'Bold', 'example' => '**bold**'],
                    ['label' => 'Italic', 'example' => '*italic*'],
                    ['label' => 'Link', 'example' => '[text](https://example.com)'],
                    ['label' => 'Bulleted list', 'example' => '- item'],
                    ['label' => 'Numbered list', 'example' => '1. item'],
                    ['label' => 'Quote', 'example' => '> quote'],
                ],
            ],
            'front_matter' => [
                'heading' => 'Page metadata',
                'description' => 'An optional block at the very top of the body sets the page’s metadata:',
                'example' => "---\ndescription: Summary shown by search engines.\neyebrow: Small label displayed above the title.\n---",
            ],
            'attachments' => 'Insert images and PDFs with the editor’s attachment button. A PDF link alone on its own line becomes a download button.',
        ],
    ],

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
