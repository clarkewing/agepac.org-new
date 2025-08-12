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

    'login' => [
        'heading' => 'Welcome back',
        'forgot-password' => 'Forgot password?',
        'remember' => 'Remember me',
        'action' => 'Log in',
        'register-prompt' => 'First time around here?',
        'register-link' => 'Sign up',
        'status' => [
            'failed' => 'These credentials do not match our records.',
            'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
        ],
    ],

    'register' => [
        'heading' => 'Register',
        'description' => 'Enter your details below to create your account.',
        'action' => 'Create account',
        'login-prompt' => 'Already have an account?',
        'login-link' => 'Log in',
    ],

    'verify-email' => [
        'heading' => 'Please verify your email address by clicking on the link we just emailed to you.',
        'action' => 'Resend verification email',
        'logout-link' => 'Log out',
        'status' => [
            'verification-link-sent' => 'A new verification link has been sent to the email address you provided during registration.',
        ],
    ],

    'forgot-password' => [
        'heading' => 'Forgot password',
        'description' => 'Enter your email to receive a password reset link.',
        'action' => 'Email password reset link',
        'login-prompt' => 'Or, return to',
        'login-link' => 'log in',
        'status' => [
            'link-sent' => __('auth.reset-password.status.sent'),
        ],
    ],

    'reset-password' => [
        'heading' => 'Reset password',
        'description' => 'Please enter your new password below.',
        'action' => 'Reset password',
        'status' => [
            'sent' => 'A reset link will be sent if the account exists.',
            'throttled' => 'Please wait before retrying.',
            'reset' => 'Your password has been reset.',
            'token' => 'This password reset token is invalid.',
            'user' => 'We can’t find a user with that email address.',
        ],
    ],

    'confirm-password' => [
        'heading' => 'Confirm password',
        'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
        'action' => 'Confirm',
        'status' => [
            'invalid-password' => 'The provided password is incorrect.',
        ],
    ],
];
