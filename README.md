## About Laravel OAuth Client

A simple OAuth2 Client implementation to test Laravel Passport and OpenID Connect.

## Installation

- git clone this repo
- composer install
- php artisan migrate
- php artisan passport:install --uuids
- php artisan passport:keys
- php artisan passport:client
You will need Client ID and Client Secret
- Fill all these in .env
  OAUTH_CLIENT_ID=
  OAUTH_CLIENT_SECRET=
  OAUTH_SERVER_URI=
  OAUTH_CLIENT_REDIRECT_URI=
  
