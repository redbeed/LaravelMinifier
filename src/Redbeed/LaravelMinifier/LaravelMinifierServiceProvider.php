<?php namespace Redbeed\LaravelMinifier;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LaravelMinifierServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{

		$configPath = __DIR__.'/../../config/config.php';
		$this->publishes([
			$configPath => config_path('laravel-minifyer.php'),
		]);

		$this->mergeConfigFrom($configPath, 'laravel-minifyer');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['laravel-minifyer'] = $this->app->share(function($app)
		{
			return new LaravelMinifier;
		});

		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Minifier', 'Redbeed\LaravelMinifier\LaravelMinifierFacade');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('laravel-minifyer');
	}

}
