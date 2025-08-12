<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fields Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for form fields across the
    | application, including their canonical labels, placeholders, and
    | tooltips.
    | Keep entries concise and reusable; prefer generic field names
    | over page-specific wording. You are free to modify these language
    | lines according to your application's requirements.
    |
    */

    'name' => [
        'label' => 'Nom',
        'placeholder' => __('fields.name.label'),
    ],
    'first-name' => [
        'label' => 'Prénom',
        'placeholder' => __('fields.first-name.label'),
    ],
    'last-name' => [
        'label' => 'Nom de famille',
        'placeholder' => __('fields.last-name.label'),
    ],
    'gender' => [
        'label' => 'Genre',
        'placeholder' => 'Sélectionner le genre…',
    ],
    'birth-date' => [
        'label' => 'Date de naissance',
        'placeholder' => __('fields.birth-date.label'),
    ],
    'class-course' => [
        'label' => 'Cursus',
        'placeholder' => 'Choisir le cursus…',
    ],
    'class-year' => [
        'label' => 'Année de promotion',
        'placeholder' => __('fields.class-year.label'),
    ],
    'email' => [
        'label' => 'Adresse e‑mail',
        'placeholder' => __('fields.email.label'),
    ],
    'phone' => [
        'label' => 'Numéro de téléphone',
        'placeholder' => __('fields.phone.label'),
        'tooltip' => 'Les numéros de téléphone internationaux sont également acceptés',
    ],
    'password' => [
        'label' => 'Mot de passe',
        'placeholder' => __('fields.password.label'),
    ],
    'password-confirmation' => [
        'label' => 'Confirmer le mot de passe',
        'placeholder' => __('fields.password-confirmation.label'),
    ],
    'current-password' => [
        'label' => 'Mot de passe actuel',
        'placeholder' => __('fields.current-password.label'),
    ],
    'new-password' => [
        'label' => 'Nouveau mot de passe',
        'placeholder' => __('fields.new-password.label'),
    ],
    'theme' => [
        'label' => 'Thème',
        'placeholder' => 'Sélectionner un thème…',
        'options' => [
            'light' => 'Clair',
            'dark' => 'Sombre',
            'system' => 'Système',
        ],
    ],
    'language' => [
        'label' => 'Langue',
        'placeholder' => 'Sélectionner une langue…',
        'options' => [
            'en' => 'Anglais',
            'fr' => 'Français',
        ],
    ],

];
