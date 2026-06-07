<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Domain
    |--------------------------------------------------------------------------
    |
    | The main domain where the app dashboard and default store subdomains
    | are hosted (e.g. manite.site). Custom store domains point to the
    | same server via A records and are resolved by host name.
    |
    */

    'platform_domain' => env('APP_PLATFORM_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Server IP Address
    |--------------------------------------------------------------------------
    |
    | Shown in the custom domain setup wizard so users know which IP to
    | point their A records to.
    |
    */

    'server_ip' => env('APP_SERVER_IP', ''),

];
