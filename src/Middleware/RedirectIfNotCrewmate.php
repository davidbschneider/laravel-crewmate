<?php

namespace DavidSchneider\LaravelCrewmate\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotCrewmate
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string  $guard
	 * @return mixed
	 *
	 * @throws \Illuminate\Auth\AuthenticationException
	 */
	public function handle($request, Closure $next, $guard = 'crewmate')
	{
		if (Auth::guard($guard)->check()) {
			return $next($request);
		}

		$redirectToRoute = $request->expectsJson() ? '' : route('crewmate.login');

		throw new AuthenticationException(
			'Unauthenticated.', [$guard], $redirectToRoute
		);
	}
}
