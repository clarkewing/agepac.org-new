<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Products
    |--------------------------------------------------------------------------
    |
    | The Stripe product ids associated with the products distributed through
    | the application. These are used in the dedicated Enums.
    |
    */

    'products' => [
        'membership' => [
            'agepac' => env('CASHIER_PRODUCT_MEMBERSHIP_AGEPAC'),
            'agepac+alumni' => env('CASHIER_PRODUCT_MEMBERSHIP_AGEPAC_ALUMNI'),
        ],
    ],

];
