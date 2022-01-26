<?php
use Illuminate\Support\Facades\Route;
require_once __DIR__ . '../../vendor/autoload.php';
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
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


# login routes
Route::get('/callback','LoginController@callback');
Route::get('/login','LoginController@login');
Route::get('/logout', 'LoginController@logout');


# pages route
Route::get('/', 'PagesController@welcome');
