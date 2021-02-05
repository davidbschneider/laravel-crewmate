<?php

namespace DavidSchneider\LaravelCrewmate\Tests;

use DavidSchneider\LaravelCrewmate\PackageServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		$this->withFactories(__DIR__.'/database/factories');
		$this->loadLaravelMigrations();
		$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
		$this->artisan('migrate', ['--database' => 'testbench'])->run();
		$this->defineRoutes(__DIR__ . '/routes/crewmate.php');
	}

	protected function getPackageProviders($app)
	{
		return [
			PackageServiceProvider::class,
		];
	}

	public function getEnvironmentSetUp($app)
	{
		// Setup default database to use sqlite :memory:
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);
	}
}
