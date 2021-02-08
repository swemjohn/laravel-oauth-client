<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The OAuth Config
    |--------------------------------------------------------------------------
    |
    | This contains the details of the OAuth Server.
    |
    */

    'client_id' => env('OAUTH_CLIENT_ID'),
    'client_secret' => env('OAUTH_CLIENT_SECRET'),
    'server_uri' => env('OAUTH_SERVER_URI'),

    'client_redirect_uri' => env('OAUTH_CLIENT_REDIRECT_URI'),

];
