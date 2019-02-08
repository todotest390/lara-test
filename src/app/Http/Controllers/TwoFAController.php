<?php

namespace App\Http\Controllers;

use App\Events\ForgetSecretKeyTrigger;
use App\User;
use App\Url2Fa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Validator;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFAController extends Controller
{
    public function __construct()
    {
    }

    public function getSetup2FA()
    {
        $user = Auth::user();
        // Initialise the 2FA class
        $google2fa = app('pragmarx.google2fa');

        // Add the secret key to the registration data
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();

        //Add the user email to the registration data
        $registration_data['email'] = $user->email;

        //Get Popup Message
//        $popupMsg = getEmailTemplate('2FA_POPUP_MESSAGE');

        // to set up two factor authentication
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
            $registration_data['google2fa_secret']
        );

        return view('google-2fa.setup', ['user' => $user, 'QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
    }

    public function enableTwoFactorAuthentication(Request $request)
    {

        $data = $request->all();
        $id = $data['user_id'];
        if (!$id) {
            return redirect('/logout');
        }
        $updateUser = User::findOrFail(decrypt($id));
        $updateUser->secret_key = encrypt($data['secret_key']);
        $updateUser->is_two_factor_enabled = 1;
        $updateUser->save();

        $secret = $data['secret_key'];

        Session::put('secret', $secret);
        Session::put('user', $id);
        Auth::logout();

        return redirect('login')->with('success','2 Factor Authentication successfully enabled');
    }

    public function disableTwoFactorAuthentication(Request $request)
    {
        $user = Auth::user();
        $user->is_two_factor_enabled = 0;
        $user->secret_key = null;
        $user->update();

        if ($request->ajax()) {
            return [
                'data' => [
                    'message' => 'success',
                    'description' => '2FA Disabled',
                ],
            ];
        }

        return redirect('setup-2fa');
    }

    public function getVerifyToken()
    {
        $user = Session::get('user');

        if($user)
        {
            $secret = Session::get('secret');
            $user = decrypt(Session::get('user'));
            return view('google-2fa.verify', compact('secret', 'user'));
        }
        else{
            return redirect('/logout')->with('message', 'User logout');
        }
    }


    public function getForget2Fa(){
        return view('google-2fa.forgetSecretKey');
    }

    public function postForget2Fa(Request $request)
    {

        $id = session('user');

        if ($id) {
            $id = decrypt($id);
            $user = User::where('id', $id)->first();
            $key = ($user->secret_key);
            $key_input = $request->key;
            if ($key) {
                if ($key_input == decrypt($key)) {

                    if ($id) {

                        $user = User::find($id);
                        //dd($user);
                        $user->is_two_factor_enabled = 0;
                        $user->secret_key = null;
                        $user->update();
                    }
                    Auth::login($user, true);
                    return redirect('/')->with('success', 'Login Successfully');
                }
            }
            return redirect('/logout')->with('success', 'User logout');
        }
    }







    public function verifyToken(Request $request)
    {

        $id = Session::get('user');

        if (!$id) {
            return redirect('/logout')->with('message', 'User logout');
        }

        $twoFaCode = ['one_time_password' => '2FA Code'];
        $messages = array();
        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required|numeric',
        ],$messages,$twoFaCode);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $secret = decrypt($request->secret);
        $one_time_password = $request->one_time_password;

        $verify = $this->verify2faToken($request,$secret, $one_time_password);

        if ($verify) {

            $user_id = decrypt(Session::get('user'));
            $request->session()->forget('user');
            $request->session()->forget('secret');

            Auth::loginUsingId($user_id);

            return redirect(config('google2fa.redirect_to'));

        } else {
            $validator = ['one_time_password' => 'Invalid 2FA Code'];
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    }

    private function verify2faToken($request,$secret,$otp)
    {

        $fa = new Google2FA($request);
        $isVerified = $fa->verifyGoogle2FA($secret, $otp);

        return $isVerified;
    }

    public function  mailSend(){

        $id = Session::get('user');
        $user = User::find(decrypt($id));
        $slug = "USER_TWO_FA_RESET";
        $encrypt_id = (decrypt($id));
        $token_id =  md5(microtime());
        //$url = env('APP_URL')."login?id=".$encrypt_id;
        $url = env('APP_URL')."/disable-google-auth?id=".$encrypt_id."&&token=".$token_id;
        $url2Fa =  Url2Fa::create([
            'url'=>$url,
            'token'=>$token_id,
        ]);
        event(new ForgetSecretKeyTrigger($user,$slug,$url));
        return redirect('login')->with('success','Mail Send Successfully');
    }

    public function disableGoogleAuth(Request $request){
        //dd($request->id);
        $token = $request->token;
        $id  = ($request->id);

        $user = User::find($id);
         $url2Fa  =  Url2Fa::where('token',$token)->first();
         //dd($url2Fa);
        if (Carbon::now()->greaterThan($url2Fa->created_at->addMinutes(30))|| $url2Fa->expired == true) {

            return redirect('/login')->with('error','The link has expired!');
        }else{
            $url2Fa->expired = true;
            $url2Fa->update();

            if($user){

                $user->is_two_factor_enabled = 0;
                $user->secret_key = null;
                $user->update();
            }

        }
        return redirect('/login')->with('success','2fa deactivated successfully');

    }
}
