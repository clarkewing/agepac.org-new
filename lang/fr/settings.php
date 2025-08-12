<?php

return [
    'title' => 'Paramètres',
    'description' => 'Gérez votre profil et les paramètres de votre compte',

    'appearance' => [
        'heading' => 'Apparence',
        'subheading' => 'Mettez à jour les paramètres d’apparence de votre compte',
        'callouts' => [
            'language-updated' => [
                'heading' => 'Langue modifiée',
                'text' => 'Votre langue a bien été modifiée. Certaines parties de l’interface peuvent nécessiter un rechargement complet de la page pour s’afficher correctement.',
                'action' => 'Recharger',
            ],
        ],
    ],

    'profile' => [
        'heading' => 'Profil',
        'subheading' => 'Mettez à jour votre nom et votre adresse e‑mail',
        'action' => 'Enregistrer',
        'email-verification' => [
            'prompt' => 'Votre adresse e‑mail n’est pas vérifiée.',
            'action' => 'Cliquez ici pour renvoyer l’e‑mail de vérification.',
            'status' => [
                'verification-link-sent' => 'Un nouveau lien de vérification a été envoyé à votre adresse e‑mail.',
            ],
        ],
        'delete-account' => [
            'heading' => 'Supprimer le compte',
            'subheading' => 'Supprimez définitivement votre compte et toutes ses ressources',
            'action' => 'Supprimer le compte',
            'confirmation-modal' => [
                'heading' => 'Êtes‑vous sûr de vouloir supprimer votre compte ?',
                'subheading' => 'Une fois votre compte supprimé, toutes ses ressources et ses données seront définitivement supprimées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.',
                'cancel-action' => 'Annuler',
            ],
            'status' => [
                'deleted' => 'Votre compte a été supprimé.',
            ],
        ],
    ],

    'password' => [
        'heading' => 'Mettre à jour le mot de passe',
        'subheading' => 'Assurez‑vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé',
        'action' => 'Enregistrer',
    ],

    'membership' => [
        'heading' => 'Cotisation',
        'subheading' => 'Gérez votre adhésion à l’AGEPAC',
        'payment-action' => 'Continuer vers le paiement',
        'manage-action' => 'Gérer l’adhésion',
        'product' => [
            'label' => 'Sélectionnez votre option d’adhésion',
        ],
        'callouts' => [
            'subscription-active' => [
                'heading' => 'Adhésion active',
                'text' => 'Vous êtes actuellement abonné à la formule :plan. Pour gérer ou annuler votre adhésion, cliquez sur le bouton « Gérer l’adhésion » ci‑dessous.',
            ],
            'subscription-trial' => [
                'heading' => 'Adhésion d’essai',
                'text' => 'Vous bénéficiez actuellement d’une adhésion d’essai qui se termine le :date. Pour vous assurer que votre adhésion se poursuive au‑delà de la période d’essai, cliquez sur le bouton « Gérer l’adhésion » ci‑dessous.',
            ],
            'subscription-ended' => [
                'heading' => 'Adhésion terminée',
                'text' => 'Votre adhésion a pris fin. Pour démarrer une nouvelle adhésion, utilisez le formulaire ci‑dessous.',
            ],
            'subscription-inactive' => [
                'heading' => 'Adhésion inactive',
                'text' => 'Vous n’avez pas d’adhésion AGEPAC active. Pour démarrer une nouvelle adhésion, utilisez le formulaire ci‑dessous.',
            ],
            'subscription-past-due' => [
                'heading' => 'Paiement en retard',
                'text' => 'Le dernier paiement de votre adhésion a échoué.',
                'action' => 'Régler le paiement',
            ],
            'subscription-incomplete' => [
                'heading' => 'Paiement incomplet',
                'text' => 'Votre paiement n’a pas pu être finalisé et nécessite une action supplémentaire.',
                'action' => 'Finaliser le paiement',
            ],
            'checkout-completed' => [
                'heading' => 'Paiement terminé',
                'text' => 'Votre paiement est en cours de traitement ; l’apparition de votre nouvelle adhésion peut prendre quelques minutes.',
            ],
            'checkout-interrupted' => [
                'heading' => 'Paiement interrompu',
                'text' => 'Il semble que le processus de paiement ait été interrompu. Lorsque vous serez prêt, veuillez réessayer.',
            ],
            'update-pending' => [
                'heading' => 'Mise à jour en attente',
                'text' => 'Une mise à jour de votre adhésion est en attente et sera appliquée lors de votre prochain cycle de facturation. Pour gérer votre adhésion, cliquez sur le bouton « Gérer l’adhésion » ci‑dessous.',
            ],
            'no-auto-renew' => [
                'heading' => 'Renouvellement automatique désactivé',
                'text' => 'Vous ne serez plus membre de l’AGEPAC à partir du :date. Vous pouvez reprendre votre adhésion en cliquant sur le bouton ci‑dessous.',
                'action' => 'Reprendre l’adhésion',
            ],
            'sepa-enticement' => [
                'heading' => 'Choisir le prélèvement SEPA nous aide à maîtriser nos coûts',
                'text' => 'C’est plus simple pour les renouvellements et cela réduit les frais — ainsi, davantage de votre contribution va là où cela compte.',
                'action' => 'En savoir plus',
            ],
        ],
    ],
];
