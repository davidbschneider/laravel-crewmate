<?php

namespace DavidSchneider\LaravelCrewmate\Controllers\Auth;

use DavidSchneider\LaravelCrewmate\Crewmate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your admins. Feel free to explore this trait.
    |
    */

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('crewmate::auth.passwords.email');
    }

    /**
     * Request a password reset mail
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if( ! $validator->fails() )
        {
            if( $user = Crewmate::where('email', $request->input('email') )->first() )
            {
                $token = Str::random(64);

                DB::table('crewmate_password_resets')->insert([
                    'email' => $user->email,
                    'token' => $token
                ]);
                $user->sendPasswordResetNotification($token);
                return redirect()->back()->with('status', trans(Password::RESET_LINK_SENT));
            }
        }
        return redirect()->back()->withErrors(['email' => trans(Password::INVALID_USER)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('crewmate');
    }
}
