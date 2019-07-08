<?php

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

Route::prefix('v1')->group(function () {
	Route::post('login', 'Api\AuthController@login');
	Route::post('register', 'Api\AuthController@register');
	Route::post('user', 'Api\AuthController@getUser');

	//? Api key protected routes
	Route::group(['middleware' => 'auth:api'], function () {
		Route::post('client_login', 'Api\ClientAuthController@login');
		Route::apiResource('clients', 'Api\ClientAuthController');
		//Password change in-app
		Route::put('clients/password_change/{client}', 'Api\ClientAuthController@passwordReset')
			->name('clients.password_change');
		//? Email verification client
		Route::get('email/resend/{id}', 'Api\ClientVerificationController@resend')->name('client.verification.resend');

		//? Appointments
		Route::apiResource('appointments', 'Api\AppointmentController', [
			'only' => ['show', 'update', 'destroy']
		]);
		Route::prefix('clients/{client}')->group(function () {
			Route::apiResource('appointments', 'Api\AppointmentController', [
				'only' => ['index', 'store']
			]);
		});
	});
});
