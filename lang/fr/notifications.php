<?php

return [
    'account-approved' => [
        'subject' => 'Votre compte AGEPAC est approuvé',
        'greeting' => 'Bonjour :name !',
        'intro' => 'Le Bureau a vérifié votre éligibilité et approuvé votre compte.',
        'access' => 'Bienvenue à bord : l’espace membres vous est désormais ouvert.',
        'action' => 'Accéder à l’espace membres',
    ],
    'user-pending-approval' => [
        'subject' => 'Nouveau membre en attente d’approbation',
        'greeting' => 'Bonjour :name !',
        'intro' => ':name (:class) vient de créer un compte AGEPAC.',
        'cta' => 'Ce compte doit être approuvé afin d’accéder à l’espace membres.',
        'action' => 'Examiner le compte',
    ],
    'verification-code' => [
        'subject' => 'Votre code de vérification',
        'greeting' => 'Bonjour :name !',
        'intro' => 'Voici votre code de vérification :',
        'expiry' => 'Il expire à :time.',
    ],
];
