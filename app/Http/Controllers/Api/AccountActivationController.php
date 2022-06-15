<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Verification\Verification;
use Illuminate\Support\Facades\Validator;
use App\Traits\ReturnTemplate;
use App\Models\Site\SiteSettings;
use Carbon\Carbon;

class AccountActivationController extends Controller
{
    use ReturnTemplate;

    function __construct(User $user, Verification $verification, SiteSettings $appSettings){
        $this->user = $user;
        $this->verification = $verification;
        $this->appSettings = $appSettings;
    }

    public function sendActivationCode(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'userId' => 'required',
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        //get the user object
        $user = $this->user->getUser([
            ['unique_id', $data['userId']],
        ]);

        if($user == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));
        }

        //send the user an email for activation of account and redirect the user to the page where they will enter code
        $verificationCode = $this->verification->createActivationCode($user, 'account-activation');

        if($verificationCode['status'] == 'success'){
            //send the activation code via email to the user
            $this->verification->sendActivationMail($verificationCode['payload'], $user);

            //return the account activation code and email
            $payload = [
                'user' => $user,  
                'token' => $verificationCode['payload']
            ];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
        }

        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }

    public function verifyAndActivateAccount(Request $request){
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'code' => 'required',
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        //get the user object
        $user = $this->user->getUser([
            ['unique_id', $data['userId']],
        ]);
            
        if($user == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));
        }

        $appSettings = $this->appSettings->getSettings();
        
        //verify the token
        $verificationCode = $this->verification->verifyTokenValidity($data['code'], 'account-activation', $user);
        if($verificationCode['status'] == 'error'){
            return $this->returnMessageTemplate(false, $verificationCode['message']);
        }

        //activation was successful, activate the user account
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        if($user->save()){

            if($appSettings->welcome_message != 'no'){
                //send welcome message to newly registerd user
                $this->verification->sendWelcomeMail($user);
            }

            $payload = ['user' => $user];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('account_verified'), $payload);
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }
}
