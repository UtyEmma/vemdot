<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\CompleteProfileRequest;
use App\Http\Requests\Api\Users\ProfileCompletionRequest;
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

    public function completeProfileSetup(UpdateUserRequest $request){
        $user = $this->user();

        $avatar = $this->uploadImageHandler($request, 'avatar', 'users');
        $id_image = $this->uploadImageHandler($request, 'id_image', 'verifications');
        $logo = $this->uploadImageHandler($request, 'logo', 'logos');

        $user->update($request->safe()->merge([
            'avatar' => $avatar,
            'verification' => 'pending',
            'id_image' => $id_image,
            'logo' => $logo
        ])->all());


        // $notification = $this->notification->text('Your Vendor Account Request has been received and is pending!')
        //                                     ->text('This process might take a few days depending ')
        //                                     ->image(asset('img/auth/login-bg.jpg'));

        // $notification->send($user, "Your Vendor Application has been received", ['mail']);

        return $this->returnMessageTemplate(true, "Your Profile has been Updated Sucessfully!", [
            'user' => $user
        ]);
    }

    public function update(UpdateUserRequest $request){
        $user = $this->user();

        $avatar = $this->uploadImageHandler($request,'avatar', 'users', $user->avatar);

        if(in_array($user->role, ['vendor', 'logistics'])){
            $id_image = $this->uploadImageHandler($request, 'id_image', 'verifications');
            $logo = $this->uploadImageHandler($request, 'logo', 'logos');
        }

        $user->update($request->safe()->merge([
            'avatar' => $avatar,
            'id_image' => $id_image ?? null,
            'logo' => $logo ?? null
        ])->all());

        return $this->returnMessageTemplate(true, "Your account was updated Successfully", $user);
    }

    public function show(){
        $user = $this->user();
        $user->notifications;
        return $this->returnMessageTemplate(true, "", [
            'user' => $user,
        ]);
    }

}
