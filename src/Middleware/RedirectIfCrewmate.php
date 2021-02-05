<?php

namespace DavidSchneider\LaravelCrewmate\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfCrewmate
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'crewmate')
	{
		if (Auth::guard($guard)->check()) {
			return redirect()->route('crewmate.home');
		}
		return $next($request);
	}
}