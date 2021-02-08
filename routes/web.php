<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    return view('welcome');
});

Route::post('/redirect', function (Request $request) {
    $state = Str::random(40);
    session(['state' => $state]);

    $scope = implode(" ",$request->permissions);

    $query = http_build_query([
        'client_id' => Config::get('oauth.client_id'),
        'redirect_uri' => Config::get('oauth.client_redirect_uri'),
        'response_type' => 'code',
        'scope' => $scope,
        'state' => $state,
    ]);
    return redirect(Config::get('oauth.server_uri') . '/oauth/authorize?' . $query);
});

Route::get('/auth/callback', function (Request $request) {
    $state = session('state');

    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );

    $response = Http::asForm()->post(Config::get('oauth.server_uri') . '/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => Config::get('oauth.client_id'),
        'client_secret' => Config::get('oauth.client_secret'),
        'redirect_uri' => Config::get('oauth.client_redirect_uri'),
        'code' => $request->code,
    ]);

    return $response->json();
});


