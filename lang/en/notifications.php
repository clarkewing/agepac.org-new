<?php

return [
    'account-approved' => [
        'subject' => 'Your AGEPAC account is approved',
        'greeting' => 'Hello :name!',
        'intro' => 'The board has checked your eligibility and approved your account.',
        'access' => 'Welcome aboard: the members area is now yours to explore.',
        'action' => 'Visit the members area',
    ],
    'user-pending-approval' => [
        'subject' => 'New member awaiting approval',
        'greeting' => 'Hello :name!',
        'intro' => ':name (:class) has just created an AGEPAC account.',
        'cta' => 'Their account must be approved before they can access the members area.',
        'action' => 'Review the account',
    ],
    'verification-code' => [
        'subject' => 'Your verification code',
        'greeting' => 'Hello :name!',
        'intro' => 'Here is your verification code:',
        'expiry' => 'It expires at :time.',
    ],
];
