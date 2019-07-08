<?php

namespace App\Providers;

use App\Console\Commands\ModelMakeCommand;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->extend('command.model.make', function ($command, $app) {
			return new ModelMakeCommand($app['files']);
		});
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Schema::defaultStringLength(192);

		//custom validator for old_password match
		Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
			return Hash::check($value, current($parameters));
		});
	}
}
