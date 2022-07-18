<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Models\Role\AccountRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller{

    public function completeProfileSetup(UpdateUserRequest $request){
        $user = $this->user();

        $user->update($request->safe()->merge([
            'kyc_status' => 'pending'
        ])->all());

        return $this->returnMessageTemplate(true,
                        "Profile Updated Sucessfully!", [
                            'user' => $user
                        ]);
    }

    public function updateDeviceId(Request $request){

        if(!$request->device_id) return $this->returnMessageTemplate(false, 'Device Id Required');
        $user = $this->user();
        $user->device_id = $request->device_id;
        $user->save();

        return $this->returnMessageTemplate(true, "", ['user' => $user]);

    }

    public function update(UpdateUserRequest $request){
        $user = $this->user();

        // check unique email except this user
        if(isset($request->email)){
            $check = User::where('email', $request->email)
                     ->where('unique_id', '!=', $user->unique_id)
                     ->count();
            if($check > 0){
                return $this->returnMessageTemplate(false, "This Email Address has already been used");
            }
        }

        $user->update($request->safe()->all());
        return $this->returnMessageTemplate(true, "Profile updated Successfully", $user);
    }

    public function show(){
        $user = $this->user();
        $user->notifications;
        $user->userRole;

        return $this->returnMessageTemplate(true, "", [
            'user' => $user,
        ]);
    }

    public function list($role){
        $query = User::where('role', $role);
        $role = AccountRole::find($role);

        if($role->name === 'Vendor'){
            $query->with(['meals']);
        }

        if($role->name === 'User'){
            $query->with(['addresses']);
        }

        if($role->name === 'Logistic'){
            $query->with(['bikes']);
        }

        // $query->notifications;

        return $this->returnMessageTemplate(true, "", [
            'users' => $query->get()
        ]);
    }

    public function single($role, $user_id){
        $user = User::findOrFail($user_id);
        $query = User::query();
        $query->with('wallet');

        if($user->userRole->name === $role) abort(400, "User is not authorized to take this action");

        if($role === 'Vendor'){
            $query->with(['meals']);
        }

        if($role === 'User'){

        }

        if($role === 'Logistic'){

        }

        $query->first();

        return $this->returnMessageTemplate(true, "", [
            'user' => $user
        ]);

    }

    function allUsers(){
        return $this->returnMessageTemplate(true, "", [
            'user' => User::with('userRole')->get()
        ]);
    }

    function fetchAccountRoles(){
        $roles = AccountRole::where('name', '!=', 'Super Admin')->where('name', '!=', 'Admin')->get();
        return $this->returnMessageTemplate(true, '', $roles);
    }

    function fetchCurrentUserRole(){
        $user = $this->user();
        $role = $user->userRole;
        return $this->returnMessageTemplate(true, '', $role);
    }
}
