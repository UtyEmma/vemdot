<?php

namespace App\Http\Controllers\Logistic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Logistic\Bike;

class RiderController extends Controller
{
    //
    protected function loginRider(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());
        
        $rider = Bike::where('email', $request->email)->first(); 
        if($rider == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));

        if(!Hash::check($request->password, $rider->password))
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));    

        if($rider->status == 'pending')
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('pending_approval'));    
            
        $token = $rider->createToken('auth_token', ['riders'])->plainTextToken;
    
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_login'), [
            'token' => $token,
            'user' => $rider,
        ]);
    }
    
    protected function logOutRider($uniqueId = null){
        if($uniqueId == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));

        $rider = Bike::where('unique_id', $uniqueId)->first();
        return $rider;
        $rider->token()->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_logout'));
    }
}
