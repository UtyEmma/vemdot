<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class KycController extends Controller{


    function fetchRequests (Request $request){
        $requests = User::where('kyc_status', $this->pending)
                        ->whereRelation('userRole', 'name', 'Vendor')->get();

        return response()->view('pages.users.kyc', [
            'users' => $requests
        ]);
    }

    function updateStatus(Request $request, NotificationService $notificationService, $user_id){
        $user = User::find($user_id);
        // ['confirmed', 'pending', 'declined']
        $user->kyc_status = $request->status;
        $user->status = $this->confirmed;
        $user->save();

        if ($request->status === $this->confirmed) {
            $notificationService->text('Congratulations, your '.env('APP_NAME').' account has been approved!')
                                ->text("You can now proceed to your application and enjoy the amazing benefits offered on the ".env('APP_NAME')." platform.")
                                ->send($user, ['mail']);
        }else if($request->status === $this->declined){
            $notificationService->text('Sorry, we could not approve your account verification request at this time because "'.$request->reason.'"')
                                ->text("Please update your information provided on your application and try again!")
                                ->text("You can reach out to our support center via ".env('SUPPORT_EMAIL'))
                                ->send($user, ['mail']);
        }


        return redirect()->back()->with('message', "User KYC Request has been $request->status");
    }



}
