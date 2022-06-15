<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ReturnTemplate;
use App\Models\Site\SiteSettings;
use App\Models\Verification\Verification;
use Carbon\Carbon;

class LoginController extends Controller
{
    use ReturnTemplate;

    function __construct(SiteSettings $appSettings, Verification $verification){
        $this->appSettings = $appSettings;
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

        $token = $user->createToken('auth_token', ['auth:give_access'])->plainTextToken;

        //check if the user is blocked
        if($user->status === 'inactive'){
            $this->logoutUser();
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('account_blocked'));
        }

        $appSettings = $this->appSettings->getSettings();

        if($appSettings->account_verification != 'no'){
            //check for unactivated account
            if($user->email_verified_at == null){
                //send the user an email for activation of account and redirect the user to the page where they will enter code
                $verificationCode = $this->verification->createActivationCode($user);
                if($verificationCode['status'] == 'success'){
                    //send the activation code via email to the user
                    $this->verification->sendActivationMail($verificationCode['payload'], $user);

                    $this->logoutUser();
    
                    //return the account activation code and email
                    $payload = [
                        'user' => $user,  
                        'token' => $verificationCode['payload']
                    ];
                    return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
                }
            }
        }

        if($appSettings->login_alert != 'no'){
            $currentDate = Carbon::now();
            $dateFormat = $currentDate->format('l jS \\of F Y h:i:s A'); 
            //send login notifier to users
            $this->verification->procastLoginMailToUser($user, $dateFormat);
        }

        $payload = [
            'token' => $token,
            'user' => $user,
        ];
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_login'), $payload);
    }

    // method for user logoutUser and delete token
    public function logoutUser(){
        auth()->user()->tokens()->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_logout'));
    }
}
