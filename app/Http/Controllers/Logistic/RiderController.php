<?php

namespace App\Http\Controllers\Logistic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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

        if (!Auth::attempt($request->only('email', 'password')))
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));    
        
        $rider = User::where('email', $request->email)->firstOrFail(); 

        if($rider->status == 'pending'){
            $this->logoutUser();
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('pending_approval'));   
        } 
            
        $token = $rider->createToken('auth_token', ['riders'])->plainTextToken;
    
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_login'), [
            'token' => $token,
            'user' => $rider,
        ]);
    }
}
