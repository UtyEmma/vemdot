<?php

namespace App\Traits;
use App\Models\User;
use App\Models\UserCode;
use App\Traits\ReturnTemplate;
use Carbon\Carbon;

trait VerifyTwoFactor {
    use ReturnTemplate;

    public function verifyTwofactor($data)
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
}