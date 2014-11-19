<?php namespace LoveSick\Providers;

use Config;
use Facebook\FacebookSession;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		FacebookSession::setDefaultApplication(
			Config::get('services.facebook.client_id'),
			Config::get('services.facebook.client_secret')
		);

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
