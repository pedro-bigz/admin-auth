<?php

return [

    /*
    |
    | This option controls which defaults are used for admin users
    |
    */

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'admin_users',
        'activations' => 'admin_users',
    ],

    /*
    |
    | This option controls if Login should check also forbidden key
    |
    */

    'token_type' => env('TOKEN_TYPE', 'bearer'),

    /*
    |
    | This option controls if Login should check also forbidden key
    |
    */

    'check_forbidden' => env('FORBIDDEN_ENABLED', false),

    /*
    |
    | This option controls if Login should check also enabled key
    |
    */

    'activation_enabled' => env('ACTIVATION_ENABLED', false),

    /*
    |
    | This option handles the self activation form accessibility.
    |
    */

    'self_activation_form_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | This option controls if package routes are used or not
    |
    */

    'use_routes' => true,
];
