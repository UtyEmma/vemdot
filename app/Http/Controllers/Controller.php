<?php

namespace App\Http\Controllers;

use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Models\UserCode;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ReturnTemplate, Generics,  FileUpload;

    protected $notification;

    function __construct(NotificationService $notificationService){
        $this->notification = $notificationService;
    }

    protected function user (){
        $user = Auth::user();
        return $user ? User::findOrFail($user->unique_id) : null;
    }

    protected function getSiteSettings(){
        $settings = new SiteSettings();
        return $settings->getSettings();
    }

    protected function verifyTwofactor($data)
    {
        $user = User::where('unique_id', $data['user_id'])
            ->first();
       
        $find = UserCode::where('user_id', $user->unique_id)
            ->where('code', $data['code'])
            ->first();
           
        if($find->status == 'used'){
            return ['status' => false, 'message' => $this->returnErrorMessage('used_code')];
        } 

        //add thirty minutes to the time for the code that was created
        $currentTime = Carbon::now()->toDateTimeString();
        $expirationTime = Carbon::parse($find->created_at)->addMinutes(30)->toDateTimeString();
        //compare the dates
        if ($currentTime > $expirationTime) {
            return ['status' => false, 'message' => $this->returnErrorMessage('expired_token')];
        }
        
        if (!is_null($find)) {
            $user->two_factor_verified_at = Carbon::now()->toDateTimeString();
            $user->save();
            
            $find->status = 'used';
            $find->save();
            
            $find->users;

            return ['status' => true, 'payload' => $find];
        }
        return ['status' => false, 'message' => $this->returnErrorMessage('wrong_code')];
    }

    protected function resend2faCode()
    {
        $data = $this->user()->generateCode();

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $data['code']);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }
}
