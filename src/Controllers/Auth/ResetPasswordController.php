<?php

namespace DavidSchneider\LaravelCrewmate\Controllers\Auth;

use DavidSchneider\LaravelCrewmate\Crewmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Where to redirect admins after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = 'crewmate.home';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm($token = null)
    {
        if($this->validateToken($token))
        {
            return view('crewmate::auth.passwords.reset')
                ->withToken($token);
        } else {
	        return redirect()->back()->withErrors(['email' => trans(Password::INVALID_USER)]);
            return redirect(route('crewmate.password.request'));
        }
    }

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Exception
	 */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:crewmates,email',
            'password' => 'required|confirmed',
        ]);
        if(DB::table('crewmate_password_resets')->where([
            ['email', '=', $request->get('email')],
            ['token', '=', $request->get('token')]
        ])->count())
        {
            Crewmate::where('email', $request->get('email'))->firstOrFail()->update([
            	'password' => Hash::make($request->get('password')),
	        ]);
            return redirect(route('crewmate.login'));
        } else {
        	throw new \Exception("Deal with me");
        }
    }

	/**
	 * Validate token
	 *
	 * @param $token
	 *
	 * @return bool
	 */
    protected function validateToken($token)
    {
        return 0 < DB::table('crewmate_password_resets')
            ->where('token', $token)->count();
    }
}
