<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers\Api',
    'prefix' => 'auth'
],function($router){
    Route::post('login','AuthController@login');
    Route::post('register','AuthController@register');
    Route::post('logout','AuthController@logout');
    Route::get('profile','AuthController@profile');
    Route::post('refresh','AuthController@refresh');
    Route::post('update','AuthController@update');
    Route::DELETE('delete/{u_id}','AuthController@destroy');
    Route::get('books','AuthController@books');
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers\Api'
],function($router){
    Route::resource('books','BookController');
    Route::get('books/rent-book/{b_id}','BookController@rentBook');
    Route::get('books/return-book/{b_id}','BookController@returnBook');
    Route::get('user-rented-book','BookController@rentedUsers');
});


Route::any('{any}', function(){
    return response()->json([
    	'status' => 'error',
        'message' => 'Resource not found'], 404);
})->where('any', '.*');