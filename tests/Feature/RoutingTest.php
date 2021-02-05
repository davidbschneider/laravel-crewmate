<?php

namespace DavidSchneider\LaravelCrewmate\Tests\Feature;

use DavidSchneider\LaravelCrewmate\Tests\TestCase;

class RoutingTest extends TestCase
{
	public function testDefaultRoute()
	{
		$response = $this->get(route('crewmate.default'));
		$response->assertStatus(302)
			->assertRedirect(route('crewmate.home'));
	}

	public function testLoginRoute()
	{
		$response = $this->get(route('crewmate.login'));
		$response->assertStatus(200);
	}

	public function testPasswordRequestResetRoute()
	{
		$response = $this->get(route('crewmate.password.request'));
		$response->assertStatus(200);
	}

	public function testPasswordResetResetRoute()
	{
		$response = $this->get(route('crewmate.password.reset', ['']));
		$response->assertStatus(200);
	}
}
