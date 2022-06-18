<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification\Verification;
use Carbon\Carbon;

class LoginController extends Controller
{

    function __construct(Verification $verification){
        $this->verification = $verification;
    }
    //

    public function loginUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        if (!Auth::attempt($request->only('email', 'password'))){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token', ['twofa_access'])->plainTextToken;
        
        $appSettings = $this->getSiteSettings();

        if($appSettings->account_verification != 'no'){
            //check for unactivated account
            if($user->email_verified_at == null){
                //send the user an email for activation of account and redirect the user to the page where they will enter code
                $verificationCode = $this->verification->createActivationCode($user, $appSettings);
                if($verificationCode['status'] == 'success'){
                    //send the activation code via email to the user
                    $this->verification->sendActivationMail($verificationCode['token'], $user, $appSettings);

                    $this->logoutUser();

                    //return the account activation code and email
                    $payload = [
                        'user' => $user,
                        'token' => $verificationCode['token']
                    ];
                    return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
                }
            }
        }

        //check if the user is blocked
        if($user->status == 'blocked'){
            $this->logoutUser();
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('account_blocked'));
        }

        $user->two_factor_verified_at = null;
        $user->save();

        $data = $user->generateCode();

        $payload = [
            'token' => $token,
            '2fa_code' => $data['code'],
        ];

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $payload);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }

    public function processUserlogin(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'code' => 'required',
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        $process = $this->verifyTwofactor($request);

        if($process['status']){
            
            if($this->getSiteSettings()->login_alert != 'no'){
                $currentDate = Carbon::now();
                $dateFormat = $currentDate->format('l jS \\of F Y h:i:s A');
                //send login notifier to users
                $this->verification->procastLoginMailToUser($process['payload']->users, $dateFormat, $this->getSiteSettings());
            }

            $this->logoutUser();
            $token = $process['payload']->users->createToken('auth_token', ['full_access'])->plainTextToken;

            $payload = [
                'token' => $token,
                'user' => $process['payload']->users,
            ];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_login'), $payload);

        }else{
            return $this->returnMessageTemplate(false, $process['message']);
        }
    }

    // method for user logoutUser and delete token
    public function logoutUser(){
        auth()->user()->tokens()->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_logout'));
    }
}
