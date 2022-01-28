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
Route::get('/logout', 'LoginController@logout');


# pages route
Route::get('/', 'PagesController@welcome');
Route::get('/pages', 'PagesController@pages');
Route::get('/login','PagesController@login');


# posts route
Route::get('/pages/{id}','PostsController@page');
Route::get('/pages/{id}/create','PostsController@createPost');
Route::post('/pages/{id}/create','PostsController@createPostHandler');
Route::get('/pages/{id}/{post_id}/publish','PostsController@publishPost');
Route::get('/pages/{page_id}/{post_id}/remove','PostsController@removePostConfirmation');
Route::post('/pages/{page_id}/{post_id}/remove','PostsController@removePostHandler');

Route::get('/pages/{id}/{post_id}/edit','PostsController@editPost');
Route::post('/pages/{id}/{post_id}/edit','PostsController@editPostHandler');



# my testing are
Route::get('/test', function(Request $req){
    echo $_SERVER['REQUEST_TIME'], "<br>";
    echo  strtotime("2021-02-01 21:52"), "<br>";
    echo strtotime("2022-01-27 21:52") - $_SERVER['REQUEST_TIME']; 
});

