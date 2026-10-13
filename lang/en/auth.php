<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'login' => [
        'title' => 'Log in',
        'heading' => 'Welcome back',
        'forgot-password' => 'Forgot password?',
        'remember' => 'Remember me',
        'action' => 'Log in',
        'register-prompt' => 'First time around here?',
        'register-link' => 'Sign up',
        'callouts' => [
            'lockout' => [
                'heading' => 'Too many login attempts.',
                'text' => 'You can try again in :countdown.',
                'done' => 'You can try again now.',
                'action' => 'Try again',
            ],
        ],
    ],

    'register' => [
        'title' => 'Register',
        'heading' => 'Register',
        'description' => 'Enter your details below to create your account.',
        'action' => 'Create account',
        'login-prompt' => 'Already have an account?',
        'login-link' => 'Log in',
    ],

    'verify-email' => [
        'title' => 'Verify email',
        'heading' => 'Please verify your email address by clicking on the link we emailed to :email.',
        'change-prompt' => 'Wrong address?',
        'change-link' => 'Update it',
        'action' => 'Resend verification email',
        'resend-wait' => 'You can request a new link in :countdown.',
        'logout-link' => 'Log out',
        'status' => [
            'verification-link-sent' => 'A new verification link has been sent to :email.',
        ],
    ],

    'forgot-password' => [
        'title' => 'Forgot password',
        'heading' => 'Forgot password',
        'description' => 'Enter your email to receive a password reset link.',
        'action' => 'Email password reset link',
        'login-prompt' => 'Or, return to',
        'login-link' => 'log in',
    ],

    'reset-password' => [
        'title' => 'Reset password',
        'heading' => 'Reset password',
        'description' => 'Please enter your new password below.',
        'action' => 'Reset password',
    ],

    'confirm-password' => [
        'title' => 'Confirm password',
        'heading' => 'Confirm password',
        'description' => 'This is a secure area of the website. Please confirm your password before continuing.',
        'action' => 'Confirm',
        'status' => [
            'throttled' => 'Too many attempts. Please try again in :seconds seconds.',
        ],
    ],
];
