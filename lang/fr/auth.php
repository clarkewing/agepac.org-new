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
        'steps' => [
            'identity' => [
                'label' => 'Identité',
                'heading' => 'Créez votre compte',
                'description' => 'Commencez par votre nom et votre adresse e‑mail.',
            ],
            'details' => [
                'label' => 'Informations',
                'heading' => 'Vos informations',
                'description' => 'Parlez‑nous de vous.',
            ],
            'password' => [
                'label' => 'Mot de passe',
                'heading' => 'Choisissez un mot de passe',
                'description' => 'Il protège votre compte, alors choisissez‑en un solide.',
            ],
            'review' => [
                'label' => 'Vérification',
                'heading' => 'Vous y êtes presque',
                'description' => 'Vérifiez vos informations avant de créer votre compte.',
            ],
        ],
        'action' => 'Créer mon compte',
        'login-prompt' => 'Déjà membre ?',
        'login-link' => 'Se connecter',
    ],

    'recovery' => [
        'title' => 'Récupération de votre compte',
        'heading' => [
            'password-reset' => 'Sécurisation de votre compte',
            'migration' => 'Migration de votre compte',
        ],
        'callouts' => [
            'incident' => [
                'heading' => 'Incident de sécurité',
                'text' => 'Notre site a récemment été la cible d’une cyberattaque. Par précaution, tous les mots de passe ont été invalidés.',
                'cta' => 'Choisissez-en un nouveau pour vous reconnecter.',
                'questions' => 'Des questions ?',
                'contact' => 'Contactez‑nous',
            ],
        ],
        'steps' => [
            'verify' => [
                'description' => 'Afin de confirmer votre identité, nous vous avons envoyé un e‑mail contenant un code à usage unique.',
            ],
            'details' => [
                'description' => 'Bon retour parmi nous, :name ! Commençons par mettre à jour vos informations.',
            ],
            'password' => [
                'description' => 'Choisissez un nouveau mot de passe pour votre compte AGEPAC.',
            ],
        ],
        'action' => 'Enregistrer et se connecter',
    ],

    'verification-code' => [
        'code-label' => 'Saisissez le code à 6 chiffres',
        'action' => 'Vérifier',
        'resend-prompt' => 'Vous n’avez pas reçu de code ?',
        'resend-wait' => 'Vous pourrez demander un nouveau code dans :countdown.',
        'resend-link' => 'Le renvoyer',
        'resent' => 'Un nouveau code a été envoyé à votre adresse e‑mail.',
    ],

    'pending-approval' => [
        'title' => 'En attente d’approbation',
        'heading' => 'Votre compte est en attente d’approbation',
        'greeting' => 'Bienvenue, :name !',
        'description' => 'Le Bureau vérifie l’éligibilité de chaque nouveau membre avant de lui ouvrir l’espace membres.',
        'steps' => [
            'created' => 'Compte créé',
            'verified' => 'Adresse e‑mail vérifiée',
            'verify' => 'Adresse e‑mail pas encore vérifiée',
            'verify-link' => 'La vérifier',
            'approval' => 'Approbation par le Bureau',
            'approval-hint' => 'Généralement sous 24 heures. Nous vous écrirons dès que ce sera fait.',
        ],
        'help-prompt' => 'Une question en attendant ?',
        'help-link' => 'Consulter l’aide',
        'logout-link' => 'Se déconnecter',
    ],

    'verify-email' => [
        'title' => 'Vérification de l’e‑mail',
        'heading' => 'Veuillez confirmer votre adresse e‑mail en cliquant sur le lien que nous avons envoyé à :email.',
        'change-prompt' => 'Mauvaise adresse ?',
        'change-link' => 'La modifier',
        'action' => 'Renvoyer l’e‑mail de vérification',
        'resend-wait' => 'Vous pourrez demander un nouveau lien dans :countdown.',
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
