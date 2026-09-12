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
            'title' => 'Créer une page',
            'create_another' => 'Créer puis en ajouter une autre',
        ],
        'columns' => [
            'scheduled' => 'Programmée',
            'unpublished' => 'Non publiée',
        ],
        'sections' => [
            'content' => 'Contenu',
            'settings' => 'Réglages',
        ],
        'actions' => [
            'now' => 'Maintenant (UTC)',
            'clear' => 'Effacer',
        ],
        'warnings' => [
            'public_attachments' => [
                'title' => 'Pièces jointes sur une page publique',
                'description' => 'Les pièces jointes ne sont visibles qu’aux membres approuvés. Sur une page publique, les visiteurs verront une erreur. Les pièces jointes concernées sont listées ci-dessous.',
            ],
            'converted' => 'Changer de format convertit le contenu et peut amener à la perte de données.',
        ],
        'help' => [
            'restricted' => 'Une page publique est visible par tout le monde, même sans compte.',
            'published_at' => 'La date et l’heure à partir desquelles la page devient accessible. Laissez ce champ vide pour ne pas publier la page.',
        ],
    ],

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
