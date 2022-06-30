<?php

namespace App\Http\Controllers\Logistic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Role\AccountRole;
use App\Models\User;
use Carbon\Carbon;

class LogisticController extends Controller
{
    //
    protected function createRiderRequest(Request $request){
        $data = $request->all();
        $user = $request->user();
        $validator = Validator::make($data, [
            'role' => 'required',
            'phone' => 'required',
            'name' => 'required|between:2,100',
            'email' => 'required|email|unique:users|max:50',
            'password' => ['required', Rules\Password::defaults()],
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());
        //get user role
        $userRole = AccountRole::find($user->role);  
        if($userRole == null) 
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_authorized'));  
        if($userRole->name != 'Logistic') 
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_authorized'));
        //create bike
        $bikeNumb = $this->createConfirmationNumbers('bikes', 'bike_no', 6);

        $rider = User::create([
            'unique_id' => $this->createUniqueId('users'),
            'business_name' => $user->unique_id,
            'role' => $data['role'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'id_number' => $bikeNumb,
            'logo' => $data['bike_image'],
            'referral_id' => $this->createUniqueId('users', 'referral_id'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'two_factor' => 'yes',
        ]);
        //notify logistic about new bike/ride
        $notification = $this->notification();
        $notification->subject('New Rider Was Added')
            ->text('A new rider was successfully added. Please wait for the admin to approve your bike.')
            ->text('Below are the details of the new rider:')
            ->text('Rider Name: '.$data['name'])
            ->code($bikeNumb)
            ->text('Rider Email: '.$data['email'])
            ->text('Rider Password: '.$data['password'])
            ->send($user, ['mail', 'database']);
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Rider'), $rider);
    }

    protected function fetchAllRiders($logistic = null){
        if($logistic == null)
            $logistic = $this->user()->unique_id;
        $riders = User::where('business_name', $logistic)
            ->orderBy('id', 'desc')
            ->get();
        if(count($riders) > 0){
            foreach($riders as $rider){
                $rider->logistic;
            }
        }
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Riders'), $riders);
    }

    protected function fetchSingleRider($uniqueId = null){
        if($uniqueId == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        $rider = User::where('unique_id', $uniqueId)->first();  
        if($rider == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Rider'));
        $rider->logistic;
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_single', 'Rider'), $rider);
    }

    protected function updateRiderDetails(Request $request, $uniqueId = null){
        $data = $request->all();
        if($uniqueId == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        //update bike
        $rider = User::where('unique_id', $uniqueId)->first();
        if($rider == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Rider'));
        $rider->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'avatar' => $data['avatar'] == null ? $rider->avatar : $data['avatar'],
            'logo' => $data['bike_image'],
        ]);
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Rider'), $rider);
    }

    protected function deleteRiders($uniqueId = null){
        if($uniqueId == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        $rider = User::where('unique_id', $uniqueId)->first();
        if($rider == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Rider'));
        $rider->delete();
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', 'Rider'));
    }

    protected function updateRiderAvaliablity(Request $request){
           $data = $request->all();
           $validator = Validator::make($data, [
               'status' => 'required',
               'uniqueId' => 'required',
           ]);
           if($validator->fails())
               return $this->returnMessageTemplate(false, $validator->messages());
           //update rider availability
           $rider = User::where('unique_id', $data['uniqueId'])->first();
           if($rider == null)
               return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Rider'));
           $rider->update([
               'availability' => $data['status'],
           ]);
           return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Rider Avaliability'), $rider);
       }

  

}