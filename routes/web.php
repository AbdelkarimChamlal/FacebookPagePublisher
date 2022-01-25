<?php
use Illuminate\Support\Facades\Route;
require_once __DIR__ . '../../vendor/autoload.php';
use Illuminate\Http\Request;

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

Route::get('/', function () {

    $app_id = Config::get('services.facebookApp.id');
    $verion = Config::get('services.facebookApp.api_version');
    $callBack = Config::get('services.facebookApp.callback');
    $permissions = Config::get('services.facebookApp.permissions');
    $loginUrl = "https://www.facebook.com/v12.0/dialog/oauth?client_id=$app_id&response_type=code&redirect_uri=$callBack&scope=$permissions";
    
    echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
});


Route::get('/callback','LoginController@callback');

Route::get('/welcome', function(){
    return view('welcome');
});
