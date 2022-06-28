<?php

namespace App\Http\Controllers\Logistic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Logistic\Bike;
use App\Models\Role\AccountRole;

class LogisticController extends Controller
{
    //
    protected function createRiderRequest(Request $request){
        $data = $request->all();
        $user = $request->user();
        $validator = Validator::make($data, [
            'name' => 'required|between:2,100',
            'email' => 'required|email|unique:bikes|max:50',
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
        $bike = Bike::create([
            'unique_id' => $this->createUniqueId('bikes'),
            'user_id' => $user->unique_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'bike_no' => $bikeNumb,
            'bike_image' => $data['bike_image'],
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
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Rider'), $bike);
    }

    protected function fetchAllRiders($logistic = null){
        if($logistic == null)
            $logistic = $this->user()->unique_id;
        $riders = Bike::where('user_id', $logistic)->orderBy('id', 'desc')->get();
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

        $rider = Bike::where('unique_id', $uniqueId)->first();    
        $rider->logistic;
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_single', 'Rider'), $rider);
    }

    protected function updateRiderDetails(Request $request, $uniqueId = null){
        $data = $request->all();
        if($uniqueId == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        //update bike
        $bike = Bike::where('unique_id', $uniqueId)->first();
        if($bike == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Rider'));
        $bike->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar' => $data['avatar'] == null ? $bike->avatar : $data['avatar'],
            'bike_image' => $data['bike_image'],
        ]);
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Rider'), $bike);
    }
}
