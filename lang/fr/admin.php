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
        'label' => 'utilisateur',
        'sections' => [
            'identity' => 'Identité',
            'account' => 'Compte',
            'class' => 'Promotion',
            'history' => 'Historique',
        ],
        'columns' => [
            'class' => 'Promotion',
        ],
        'filters' => [
            'approved' => 'Approuvé',
        ],
        'timestamps' => [
            'created_at' => 'Inscrit le',
            'updated_at' => 'Modifié le',
            'email_verified_at' => 'E‑mail vérifié le',
            'approved_at' => 'Approuvé le',
        ],
        'actions' => [
            'approve' => [
                'label' => 'Approuver',
                'success' => 'Utilisateur approuvé.',
                'modal' => [
                    'heading' => 'Approuver cet utilisateur ?',
                    'description' => 'L’utilisateur aura accès à l’espace membres.',
                ],
            ],
            'assign-role' => [
                'label' => 'Attribuer un rôle',
                'placeholder' => 'Aucun',
                'success' => 'Rôle mis à jour.',
                'unapproved' => 'L’utilisateur doit être approuvé avant de pouvoir recevoir un rôle.',
            ],
            'send-password-reset' => [
                'label' => 'Réinitialiser',
                'success' => 'Lien de réinitialisation envoyé.',
                'modal' => [
                    'heading' => 'Envoyer un lien de réinitialisation ?',
                    'description' => 'Un e‑mail contenant un lien de réinitialisation sera envoyé à :email.',
                ],
            ],
            'resend-verification' => [
                'label' => 'Renvoyer',
                'success' => 'E‑mail de vérification envoyé.',
                'modal' => [
                    'heading' => 'Renvoyer l’e‑mail de vérification ?',
                    'description' => 'Un nouveau lien de vérification sera envoyé à :email.',
                ],
            ],
        ],
    ],

];
