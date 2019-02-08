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

Route::get('verify-2fa', 'TwoFAController@getVerifyToken')->name('verify');
Route::post('verify-token', 'TwoFAController@verifyToken');
Route::get('forget-2fa', 'TwoFAController@getForget2Fa');
Route::post('secret-key-submit', 'TwoFAController@postForget2Fa');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('users', 'UserController');
    Route::get('setup-2fa', 'TwoFAController@getSetup2FA');
    Route::post('enable-2fa', 'TwoFAController@enableTwoFactorAuthentication');
    Route::post('disable-2fa', 'TwoFAController@disableTwoFactorAuthentication');

});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
