<?php

namespace App\Models\Verification;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;

use App\Models\Role\AccountRole;
use App\Services\NotificationService;
use Carbon\Carbon;

class Verification extends Model
{
    use HasFactory, SoftDeletes, Generics, ReturnTemplate;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getVerification($condition, $id = 'id', $desc = "desc"){
        return Verification::where($condition)->orderBy($id, $desc)->get();
    }

    public function getSingleVerification($condition){
        return Verification::where($condition)->first();
    }

    public function createActivationCode($user, $appSettings, $type = "account-activation") {

        //check if there is an existing code for current type of action
        $codeDetails  = $this->getSingleVerification([
            ["user_id", "=", $user->unique_id],
            ["status", "=", "un-used"],
            ["type", "=", $type],
        ]);

        //check if the query returned null
        if($codeDetails !== null){
            $codeDetails->status = 'failed';
            $codeDetails->save();
        }

        $token = $this->createConfirmationNumbers('verifications', 'token', $appSettings->token_length);

        //call the function that creates the confirmation code
        $dataToSave = $this->returnObject([
            'unique_id' => $this->createUniqueId('verifications', 'unique_id'),
            'user_id' => $user->unique_id,
            'token' => $token,
            'type' => $type,
        ]);

        $verification = $this->createVerification($dataToSave);
        $verification->status = 'success';
        return $verification;
    }

    //create new confirmation code
    function createVerification($request){
        $Verification = new Verification();
        $Verification->unique_id = $request->unique_id;
        $Verification->user_id = $request->user_id;
        $Verification->token = $request->token;
        $Verification->type = $request->type;
        $Verification->status = 'un-used';
        $Verification->save();
        return $Verification;
    }

    //verify token
    function verifyTokenValidity($token, string $token_type, $user) : array {
        try{
            //validate the token from the verification table
            $tokenDetails = $this->getSingleVerification([
                ["user_id", $user->unique_id],
                ["token", $token],
                ["type", $token_type],
            ]);

            //send the error message to the view
            if($tokenDetails === null){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('invalid_token'));
            }

            //add fifty minutes to the time for the code that was created
            $currentTime = Carbon::now()->toDateTimeString();
            $expirationTime = Carbon::parse($tokenDetails->created_at)->addMinutes(50)->toDateTimeString();
            //compare the dates
            if ($currentTime > $expirationTime) {
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('expired_token'));
            }

            //mark token as used token
            $tokenDetails->status = "used";
            $tokenDetails->save();
            //return the true status to the front end
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('valid_token'));
        }catch(\Exception $e){
            return $this->returnMessageTemplate(false, $e->getMessage());
        }
    }

    //send the email to the user involved
    function sendActivationMail($token, $user, $appSettings){
        $role = AccountRole::find($user->role);
        $notification = new NotificationService();
        $notification->subject("Activate your ".$appSettings->site_name.' '.$role->name ?? ''." Account")
            ->greeting('How you dey?')
            ->text('Your have successfully created an account with' .ucfirst($appSettings->site_name))
            ->text("Below is a ".$appSettings->token_length.' digit code for the activation of your account. Please provide this code in your App to proceed')
            ->code($token)
            ->text('Thanks for being part of the'.ucfirst($appSettings->site_name).'family.')
            ->text("We are glad and pleased to have you on board, feel free to explore our platform and enjoy our services.")
            ->data('This is the notification Message')
            ->send($user, ['mail', 'database']);
    }

    //send the login admit mail to user
    function procastLoginMailToUser($user, $date_format, $appSettings){
        $notification = new NotificationService();
        $notification->subject(ucfirst($appSettings->site_name)." Login Alert on ".$date_format)
            ->text('There was a successful login to your '.ucfirst($appSettings->site_name).' Account')
            ->text('Please see below for login details:')
            ->code($date_format)
            ->text("If you did not login to your".ucfirst($appSettings->site_name)." account, kindly contact".ucfirst($appSettings->site_name)." (our 24/7 Live Support) or send an email to <a href='mailto::$appSettings->site_email>$appSettings->site_email</a>, stating your name and email.")
            ->data('There was a successful login to your '.ucfirst($appSettings->site_name).' Account')
            ->send($user, ['mail', 'database']);
    }

    //send the email to the user involved
    function sendWelcomeMail($user, $appSettings){
        $notification = new NotificationService();
        $notification->subject('Welcome to '.ucfirst($appSettings->site_name).'- Your number one food vendor')
            ->text('Thanks for giving '.ucfirst($appSettings->site_name).' a try')
            ->text('We’re thrilled to have you on board. We’d like you to get the most out of '.ucfirst($appSettings->site_name).', our state of the art platform is here to help you get the best out of '.ucfirst($appSettings->site_name))
            ->text('We are glad and pleased to have you on board, feel free to explore '.ucfirst($appSettings->site_name).' and enjoy our services.')
            ->send($user, ['mail']);
    }

    //send the email to the user involved
    function sendPwdResetTokenMail($token, $user, $appSettings){
        $notification = new NotificationService();
        $notification->subject('Password Reset On '.$appSettings->site_name)
            ->text('To reset the password to your '.ucfirst($appSettings->site_name).' account, provide the '.$appSettings->token_length.' digit code below to your App to proceed')
            ->code($token)
            ->text('We are glad and pleased to have you on board, feel free to explore our platform and enjoy our services.')
            ->send($user, ['mail']);
    }

    //send reset password mail to user
    function sendUserPasswordResetMail($user, $date_format, $appSettings){
        $notification = new NotificationService();
        $notification->subject('Successfull Password Reset On '.$date_format)
            ->text('You recieved this mail, because on the '.$date_format.' you placed a request to reset your password.')
            ->text('Login to your '.ucfirst($appSettings->site_name).' account to explore our awesome features')
            ->text("If you did not login to your".ucfirst($appSettings->site_name)." account, kindly contact".ucfirst($appSettings->site_name)." (our 24/7 Live Support) or send an email to <a href='mailto::$appSettings->site_email>$appSettings->site_email</a>, stating your name and email.")
            ->data('Successfull Password Reset On '.$date_format)
            ->send($user, ['mail', 'database']);
    }
}
