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
        'heading' => 'Heureux de vous revoir',
        'forgot-password' => 'Mot de passe oublié ?',
        'remember' => 'Se souvenir de moi',
        'action' => 'Se connecter',
        'register-prompt' => 'Première visite ici ?',
        'register-link' => 'S’inscrire',
        'status' => [
            'failed' => 'Les identifiants saisis sont erronés.',
            'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',
        ],
    ],

    'register' => [
        'heading' => 'Inscription',
        'description' => 'Saisissez vos informations ci‑dessous pour créer votre compte.',
        'action' => 'Créer un compte',
        'login-prompt' => 'Vous avez déjà un compte ?',
        'login-link' => 'Se connecter',
    ],

    'verify-email' => [
        'heading' => 'Veuillez confirmer votre adresse e‑mail en cliquant sur le lien que nous venons de vous envoyer.',
        'action' => 'Renvoyer l’e‑mail de vérification',
        'logout-link' => 'Se déconnecter',
        'status' => [
            'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à l’adresse e‑mail fournie lors de l’inscription.',
        ],
    ],

    'forgot-password' => [
        'heading' => 'Mot de passe oublié',
        'description' => 'Saisissez votre e‑mail pour recevoir un lien de réinitialisation.',
        'action' => 'Envoyer le lien de réinitialisation',
        'login-prompt' => 'Ou, revenir à',
        'login-link' => 'la connexion',
        'status' => [
            'link-sent' => 'Un lien de réinitialisation sera envoyé si le compte existe.',
        ],
    ],

    'reset-password' => [
        'heading' => 'Réinitialiser le mot de passe',
        'description' => 'Veuillez saisir votre nouveau mot de passe ci‑dessous.',
        'action' => 'Réinitialiser',
        'status' => [
            'sent' => 'Un lien de réinitialisation sera envoyé si le compte existe.',
            'throttled' => 'Veuillez patienter avant de réessayer.',
            'reset' => 'Votre mot de passe a été réinitialisé.',
            'token' => 'Ce jeton de réinitialisation de mot de passe n’est pas valide.',
            'user' => 'Nous ne trouvons pas d’utilisateur avec cette adresse e‑mail.',
        ],
    ],

    'confirm-password' => [
        'heading' => 'Confirmer le mot de passe',
        'description' => 'Il s’agit d’une zone sécurisée de l’application. Veuillez confirmer votre mot de passe avant de continuer.',
        'action' => 'Confirmer',
        'status' => [
            'invalid-password' => 'Le mot de passe fourni est erroné.',
        ],
    ],
];
