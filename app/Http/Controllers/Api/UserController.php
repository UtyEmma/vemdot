<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\CompleteProfileRequest;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Models\Restaurant\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Users\KycVerification;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{
    use ReturnTemplate, FileUpload, Generics;

    public function list(Request $request)
    {
        return response([
                'users' => User::all(),
                'success' => 1
            ]);
    }


    public function store(Request $request){
        $request->validate([
            'name'     => 'required | string ',
            'email'    => 'required | email | unique:users',
            'password' => 'required | confirmed',
            'role'     => 'required'
        ]);

        // store user information
        $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password)
                ]);

        // assign new role to the user
        $role = $user->assignRole($request->role);

        if($user){
            return response([
                'message' => 'User created succesfully!',
                'user'    => $user,
                'success' => 1
            ]);
        }

        return response([
                'message' => 'Sorry! Failed to create user!',
                'success' => 0
            ]);
    }

    public function profile($id, Request $request)
    {
        $user = User::find($id);
        if($user)
            return response(['user' => $user,'success' => 1]);
        else
            return response(['message' => 'Sorry! Not found!','success' => 0]);
    }


    public function delete($id, Request $request)
    {
        $user = User::find($id);

        if($user){
            $user->delete();
            return response(['message' => 'User has been deleted','success' => 1]);
        }
        else
            return response(['message' => 'Sorry! Not found!','success' => 0]);
    }


    public function changeRole($id,Request $request){
        $request->validate([
            'roles'     => 'required'
        ]);

        // update user roles
        $user = User::find($id);
        if($user){
            // assign role to user
            $user->syncRoles($request->roles);
            return response([
                'message' => 'Roles changed successfully!',
                'success' => 1
            ]);
        }

        return response([
                'message' => 'Sorry! User not found',
                'success' => 0
            ]);
    }

    public function update(UpdateUserRequest $request, $id){
        if(!$user = User::find($id))
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));

        $avatar = $this->uploadImageHandler($request,'avatar', 'users', $user->avatar);

        $user->update($request->safe()->merge(['avatar' => $avatar])->all());

        return $this->returnMessageTemplate(true, "Your account was updated Successfully", $user);
    }

    public function show(){
        $user = Auth::user();
        return $this->returnMessageTemplate(true, "", $user);
    }

}
