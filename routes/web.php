<?php

use CoderCat\JWKToPEM\JWKConverter;
use Firebase\JWT\JWT;
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
    $loggedIn = $request->session()->get('oauth_access_token')? true : false;
    $id_token = $request->session()->get('id_token');
    $id_token = $id_token ? print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $id_token)[1])))), true) : "";
    //dd($id_token);
    return view('welcome', compact('loggedIn', 'id_token'));
});

Route::post('/redirect', function (Request $request) {
    $state = Str::random(40);
    session(['state' => $state]);

    $scope =  implode(" ",$request->permissions);
   // dd($scope);

    $query = http_build_query([
        'client_id' => Config::get('oauth.client_id'),
        'redirect_uri' => Config::get('oauth.client_redirect_uri'),
        'response_type' => 'code',
        'scope' =>  $scope,
        'state' => $state,
    ]);
    return redirect(Config::get('oauth.server_uri') . '/oauth/openid/v1/authorize?' . $query);
});

Route::get('/auth/callback', function (Request $request) {
    $state = $request->session()->pull('state');

    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );
  // dd($request->code);
    $response = Http::asForm()->post(Config::get('oauth.server_uri') . '/oauth/openid/v1/token', [
        'grant_type' => 'authorization_code',
        'client_id' => Config::get('oauth.client_id'),
        'client_secret' => Config::get('oauth.client_secret'),
        'redirect_uri' => Config::get('oauth.client_redirect_uri'),
        'code' => $request->code,
    ]);
    if($response->successful()) {
        $resVal = $response->json();
        //dd($resVal);
        session([
            'oauth_access_token' => $resVal["access_token"],
            'oauth_expires_at' => $resVal["expires_in"],
            'oauth_refresh_token' => $resVal["refresh_token"],
            'id_token' => $resVal["id_token"] ?? ""
        ]);
    }
        $idToken = $request->session()->get('id_token');
        $response = Http::get(config('oauth.server_uri') . '/.well-known/openid/cert');
        //dd($response);
        $keys = $response->json();
        //dd($keys);
        $jwk = $keys['keys'][0];
        $jwkConverter = new JWKConverter();
        $publicKey = $jwkConverter->toPEM($jwk);
        try {
            $decoded = JWT::decode($idToken, $publicKey, array('RS256'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        //dd($decoded);

    return redirect('/');

});

Route::get('/details', function (Request $request) {
    $accessToken = $request->session()->get('oauth_access_token');
    $loggedIn = $accessToken ? true : false;
    if(!$loggedIn) {
        return redirect('/');
    }

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$accessToken,
    ])->get('http://gcs-connect.local/oauth/userinfo');

    $user = $response->json();
    return view('detail', compact('user'));

});

Route::get('/logout', function (Request $request) {
    $request->session()->flush();
   return redirect('/');
    //TODO: Also clear with Connect server
});
