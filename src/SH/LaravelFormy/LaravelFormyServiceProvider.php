<?php
namespace SH\LaravelFormy;

use Illuminate\Support\ServiceProvider;

class LaravelFormyServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->instance('laravel-formy', $this->app->make('SH\\LaravelFormy\\Factory'));
	}

	public function boot()
	{
		$this->package('sahanh/laravel-formy');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
