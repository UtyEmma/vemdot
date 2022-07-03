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

    function __construct(Verification $verification, User $user){
        $this->verification = $verification;
        $this->user = $user;
    }
    //

    public function loginUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());

        if (!Auth::attempt($request->only('email', 'password')))
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));

        $user = User::where('email', $request['email'])->firstOrFail();
        
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
        if($user->status == $this->pending){
            $this->logoutUser();
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('account_blocked'));
        }

        $user->two_factor_verified_at = null;
        $user->save();

        if($user->two_factor_access == 'text'){
            $data = $user->generateCode($user);
        }else{
            $data = $user->generateCodeFor2fa($user);
            $notification = $this->notification();
            $notification->subject("2FA On ".$appSettings->site_name)
            ->text('Your 2FA login code is ')
            ->code($data['code'])
            ->text('provide this code on your app to authenticate your login')
            ->send($user, ['mail']);
        }

        $payload = [
            '2fa_code' => $data['code'],
            'user' => $user,
        ];

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $payload);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }

    public function processUserlogin(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'user_id' => 'required',
            'code' => 'required',
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        $process = $this->verifyTwofactor($data);

        if($process['status']){
            
            if($this->getSiteSettings()->login_alert != 'no'){
                $currentDate = Carbon::now();
                $dateFormat = $currentDate->format('l jS \\of F Y h:i:s A');
                //send login notifier to users
                $this->verification->procastLoginMailToUser($process['payload']->users, $dateFormat, $this->getSiteSettings());
            }

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
}
