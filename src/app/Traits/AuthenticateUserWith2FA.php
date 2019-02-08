<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use OTPHP\Factory;
use OTPHP\TOTP;
use Illuminate\Validation\Validator;

trait AuthenticateUserWith2FA
{
    /*
     * Private variable to store user object.
     */
    private $user;

    /**
     * If username/password is authenticated then the authenticted.
     * If 2FA enabled it will redirect user to enter TOTP Token else
     * Logs the user in normally.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_two_factor_enabled) {
            $request->session()->flush(); // remove all the session data
            $request->session()->put('user', encrypt($user->id));
            $request->session()->put('secret', $user->secret_key);
            Auth::logout();

            return redirect()->intended('verify-2fa');
        }

        return redirect()->intended(config('google2fa.redirect_to'));
    }



    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
