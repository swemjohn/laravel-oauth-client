## About Laravel OAuth Client

A simple OAuth2 Client implementation to test Laravel Passport and OpenID Connect.

## Installation

- git clone this repo
- composer install

You will need Client ID and Client Secret
- Fill all these in .env
  
  OAUTH_CLIENT_ID=
  
  OAUTH_CLIENT_SECRET=
  
  OAUTH_SERVER_URI=
  
  OAUTH_CLIENT_REDIRECT_URI=

  To get client ID and secret run these commands on your Laravel Passport project
    - php artisan passport:client
    - The OAUTH_SERVER_URI is the URL to your Passport Server
    - The OAUTH_CLIENT_REDIRECT_URL is the URL of this client if your URL is http://oauth-client.local it should be http://oauth-client.local/auth/callback

    
