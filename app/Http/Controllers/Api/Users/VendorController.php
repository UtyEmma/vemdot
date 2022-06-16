<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\VendorAccountRequest;
use App\Models\Restaurant\Restaurant;
use App\Models\Users\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller{

    public function vendorAccountRequest(VendorAccountRequest $request){
        $user = $this->user;

        $avatar = $this->uploadImageHandler($request, 'avatar', 'users');
        $id_image = $this->uploadImageHandler($request, 'id_image', 'verifications');

        $verification = KycVerification::create($request->safe()
                                        ->merge([
                                            'user_id' => $user->unique_id,
                                            'id_image' => $id_image,
                                            'unique_id' => $this->createUniqueId('users', 'unique_id')
                                        ])->only(['unique_id', 'user_id', 'id_number', 'id_image']));

        $user->update($request->safe()->merge([
            'avatar' => $avatar,
            'verification' => 'pending'
        ])->only(['phone', 'avatar', 'name', 'verification']));

        $logo = $this->uploadImageHandler($request, 'logo', 'restaurant');

        $restaurant = Restaurant::create($request->safe()
                                    ->merge([
                                        'user_id' => $user->unique_id,
                                        'unique_id' => $this->createUniqueId('restaurants', 'unique_id'),
                                        'name' => $request->resturant_name,
                                        'logo' => $logo
                                    ])->only(['name', 'unique_id', 'user_id', 'logo', 'city', 'state', 'address', 'avg_time']));

        $notification = $this->notification->text('Your Vendor Account Request has been received and is pending!')
                                            ->text('This process might take a few days depending ')
                                            ->image(asset('img/auth/login-bg.jpg'));

        $notification->send($user, "Your Vendor Application has been received", ['mail']);

        return $this->returnMessageTemplate(true, "Your Profile has been Updated Sucessfully!", [
            'user' => $user,
            'verification' => $verification,
            'restaurant' => $restaurant
        ]);
    }

}
