<?php

namespace DavidSchneider\LaravelCrewmate\Controllers\Auth;

use DavidSchneider\LaravelCrewmate\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admins for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectTo = 'crewmate.home';

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('crewmate');
    }

    /**
     * Display the login view.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return view('crewmate::auth.login');
    }

	/**
	 * Handle an incoming authentication request.
	 *
	 * @param LoginRequest $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws ValidationException
	 */
    public function login(LoginRequest $request)
    {
        $request->ensureIsNotRateLimited();
        if (! $this->guard()->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            RateLimiter::hit($request->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        RateLimiter::clear($request->throttleKey());
        $request->session()->regenerate();
        return redirect()->intended(route($this->redirectTo));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
