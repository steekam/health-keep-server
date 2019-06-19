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

Route::get('/', 'HomeController@index');

Auth::routes();

// Password reset routes for clients
Route::prefix('client')->group(function () {
	Route::post('password/email', 'Client\Auth\ClientForgotPasswordController@sendResetLinkEmail')->name('client.password.email');
	Route::get('password/reset', 'Client\Auth\ClientForgotPasswordController@showLinkRequestForm')->name('client.password.request');
	Route::post('password/reset', 'Client\Auth\ClientResetPasswordController@reset')->name('client.password.update');
	Route::get('password/reset/{token}','Client\Auth\ClientResetPasswordController@showResetForm')->name('client.password.reset');

	// General
	Route::get('home', 'Client\GeneralController@home')->name('client.home');
});

Route::get('/home', 'HomeController@index')->name('home');
