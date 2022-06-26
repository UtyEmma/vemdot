<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use App\Models\Address\Address;
use App\Models\Verification\Verification;
use Illuminate\Support\Facades\Response;

class RegisterController extends Controller
{
    //
    function __construct(Verification $verification, User $user){
        $this->verification = $verification;
        $this->user = $user;
    }

    public function register(Request $request){

        $data = $request->all();

        // return Response::json(['data' => $data]);
        $validator = Validator::make($data, [
            'name' => 'required|between:2,100',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'role' => 'required',
            'email' => 'required|email|unique:users|max:50',
            'gender' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        if($data['referred_id'] != ''){
            $users = $this->user->getUser([
                ['referral_id', $data['referred_id']]
            ]);

            if($users == null){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('refferral_not_found'));
            }
        }

        $user = User::create([
            'unique_id' =>  $this->createUniqueId('users', 'unique_id'),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'country' => $data['country'],
            'gender' => $data['gender'],
            'referral_id' => $this->createUniqueId('users', 'referral_id'),
            'referred_id' => $data['referred_id'] ? $data['referred_id'] : null,
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        $address = new Address();
        $address->unique_id  = $this->createUniqueId('addresses', 'unique_id');
        $address->user_id = $user->unique_id;
        $address->city = $data['city'];
        $address->state = $data['state'];
        $address->location = $data['address'];
        $address->default = "yes";
        $address->save();

        $appSettings = $this->getSiteSettings();

        if($appSettings->account_verification != 'no'){
            //send the user an email for activation of account and redirect the user to the page where they will enter code
            $verificationCode = $this->verification->createActivationCode($user, $appSettings);
            // dd($verificationCode);
            if($verificationCode['status'] == 'success'){
                //send the activation code via email to the user
                $this->verification->sendActivationMail($verificationCode['token'], $user, $appSettings);

                //return the account activation code and email
                $payload = [
                    'user' => $user,
                    'token' => $verificationCode['token']
                ];

                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
            }
        }

        if($appSettings->welcome_message != 'no'){
            //send welcome message to newly registerd user
            $this->verification->sendWelcomeMail($user, $appSettings);
        }

        $payload = ['user' => $user];

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('account_registered'), $payload);
    }
}
