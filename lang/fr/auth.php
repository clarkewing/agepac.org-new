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

    'failed' => 'Les identifiants saisis sont erronés.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

    'login' => [
        'title' => 'Connexion',
        'heading' => 'Heureux de vous revoir',
        'forgot-password' => 'Mot de passe oublié ?',
        'remember' => 'Se souvenir de moi',
        'action' => 'Se connecter',
        'register-prompt' => 'Première visite ?',
        'register-link' => 'Nous rejoindre',
        'callouts' => [
            'lockout' => [
                'heading' => 'Trop de tentatives de connexion.',
                'text' => 'Vous pourrez réessayer dans :countdown.',
                'done' => 'Vous pouvez réessayer.',
                'action' => 'Réessayer',
            ],
        ],
    ],

    'register' => [
        'title' => 'Inscription',
        'heading' => 'Inscription',
        'description' => 'Saisissez vos informations ci‑dessous pour créer votre compte.',
        'action' => 'Créer un compte',
        'login-prompt' => 'Déjà membre ?',
        'login-link' => 'Se connecter',
    ],

    'verify-email' => [
        'title' => 'Vérification de l’e‑mail',
        'heading' => 'Veuillez confirmer votre adresse e‑mail en cliquant sur le lien que nous avons envoyé à :email.',
        'change-prompt' => 'Mauvaise adresse ?',
        'change-link' => 'La modifier',
        'action' => 'Renvoyer l’e‑mail de vérification',
        'logout-link' => 'Se déconnecter',
        'status' => [
            'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à :email.',
        ],
    ],

    'forgot-password' => [
        'title' => 'Mot de passe oublié',
        'heading' => 'Mot de passe oublié',
        'description' => 'Saisissez votre e‑mail pour recevoir un lien de réinitialisation.',
        'action' => 'Envoyer le lien de réinitialisation',
        'login-prompt' => 'Ou, revenir à',
        'login-link' => 'la connexion',
    ],

    'reset-password' => [
        'title' => 'Réinitialiser le mot de passe',
        'heading' => 'Réinitialiser le mot de passe',
        'description' => 'Veuillez saisir votre nouveau mot de passe ci‑dessous.',
        'action' => 'Réinitialiser',
    ],

    'confirm-password' => [
        'title' => 'Confirmer le mot de passe',
        'heading' => 'Confirmer le mot de passe',
        'description' => 'Ceci est une zone sécurisée du site web. Veuillez confirmer votre mot de passe avant de continuer.',
        'action' => 'Confirmer',
        'status' => [
            'throttled' => 'Trop de tentatives. Veuillez réessayer dans :seconds secondes.',
        ],
    ],
];
