<?php

namespace DavidSchneider\LaravelCrewmate;

use DavidSchneider\LaravelCrewmate\Commands\InitializeAdministrator;
use DavidSchneider\LaravelCrewmate\Middleware\RedirectIfCrewmate;
use DavidSchneider\LaravelCrewmate\Middleware\RedirectIfNotCrewmate;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
	public function register()
	{
		// Merge configuration of application and package
		$this->mergeConfigFrom(__DIR__.'/../config/crewmate.php', 'crewmate');
		$this->configureAuthentication();
	}

	public function boot()
	{
		// configure routes, unless routes were already published
		if(!file_exists(database_path('routes/crewmate.php')))
		{
			$this->registerRoutes();
			$this->registerMiddleware();
		}

		// configure views
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'crewmate');

		// run only in console
		if ($this->app->runningInConsole()) {
			// load commands
			$this->commands([
				InitializeAdministrator::class,
			]);

			// publish files
			$this->publishVendorFiles();

			// load migrations
			$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		}
	}

	protected function publishVendorFiles()
	{
		// Publish assets
		$this->publishes([
			__DIR__.'/../resources/assets' => public_path('crewmate'),
		], ['crewmate-assets', 'crewmate-minimal', 'crewmate-complete']);

		// config file
		$this->publishes([
			__DIR__.'/../config/crewmate.php' => config_path('crewmate.php'),
		], ['crewmate-config', 'crewmate-complete']);

		// migration
		if (! class_exists(\CreateCrewmatePasswordResetsTable::class)) {
			$this->publishes([
				__DIR__ . '/../database' => database_path(),
			], ['crewmate-db', 'crewmate-complete']);
		}
	}

	protected function registerRoutes()
	{
		Route::group($this->routeConfiguration(), function () {
			$this->loadRoutesFrom(__DIR__.'/../routes/crewmate.php');
		});
	}

	protected function routeConfiguration()
	{
		return [
			'namespace' => 'DavidSchneider\LaravelCrewmate\Controllers',
			'prefix' => config('crewmate.routes.prefix'),
			'middleware' => config('crewmate.routes.middleware'),
		];
	}

	protected function registerMiddleware()
	{
		$router = $this->app->make(Router::class);
		$router->aliasMiddleware('crewmate.auth', RedirectIfNotCrewmate::class);
		$router->aliasMiddleware('crewmate.guest', RedirectIfCrewmate::class);
	}

	protected function configureAuthentication()
	{
		foreach(config('crewmate.groups') as $group => $config)
		{
			config(['auth.guards.'.$group => $config['guard']]);
			config(['auth.providers.'.$group => $config['provider']]);
		}
	}
}
