<?php

namespace App\Http\Controllers\Logistic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

use RealRashid\SweetAlert\Facades\Alert;

class RiderController extends Controller
{
    //
    protected function ridersInterface($logistic = null, $startDate = null, $endDate = null){
        if($logistic == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
        $logistic = User::where('unique_id', $logistic)->first();
        if($logistic == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
        $condition = [
            ['business_name', $logistic->unique_id]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }
       // return $condition;
        $rider = User::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);
        $payload = [
            'rider' => $rider,
        ];
        return view('pages.users.rider-interface', $payload);
    }

    protected function getRidersByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('vendor/interface/'.$startDate.'/'.$endDate);
    }

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
