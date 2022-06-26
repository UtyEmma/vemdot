<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller{

    public function completeProfileSetup(UpdateUserRequest $request){
        $user = $this->user();

        $user->update($request->safe()->merge([
            'verification' => 'pending'
        ])->all());

        return $this->returnMessageTemplate(true, "Your Profile has been Updated Sucessfully!", [
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request){
        $user = $this->user();
        $user->update($request->safe()->all());

        return $this->returnMessageTemplate(true, "Your account was updated Successfully", $user);
    }

    public function show(){
        $user = $this->user();
        $user->notifications;
        return $this->returnMessageTemplate(true, "", [
            'user' => $user,
        ]);
    }

    public function list($role){
        $query = User::whereRelation('userRole', 'name', $role);

        if($role === 'Vendor'){
            $query->with(['meals']);
        }

        if($role === 'User'){
            $query->with(['addresses']);
        }

        if($role === 'Logistic'){
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


}
