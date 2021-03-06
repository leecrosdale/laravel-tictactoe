<?php

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
    return view('welcome');
});

Route::get('/test', function() {


    dd(\App\Game::first()->votes);
});

Route::get('/play/update', 'GameController@update');
//Route::post('/play/pick', 'GameController@pick');

Route::post('/play/vote', 'VoteController@vote');

Route::get('/play/{team}', 'GameController@play');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
