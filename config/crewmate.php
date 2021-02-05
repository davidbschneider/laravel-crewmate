<?php

return [
	'routes' => [
		'prefix' => 'crewmate',
		'middleware' => 'web',
	],
	'groups' => [
		'crewmate' => [
			'guard' => [
				'driver' => 'session',
				'provider' => 'crewmate',
			],
			'provider' => [
				'driver' => 'eloquent',
				'model' => \DavidSchneider\LaravelCrewmate\Crewmate::class,
			],
		]
	],
];
