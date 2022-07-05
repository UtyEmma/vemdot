<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Users\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Services\NotificationService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables,Auth;

class UserController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $users = User::with('userRole')->get();

        return view('pages.users.index', [
            'users' => $users
        ]);
    }

    public function edit($user_id){
        if(!$user = User::where('unique_id', $user_id)->with('userRole')->first())
                    return redirect()->back('error', $this->returnErrorMessage('not_found', "User"));

        return view('pages.users.profile', [
            'user' => $user
        ]);
    }

    function fetchRequests (Request $request){
        $requests = User::where('kyc_status', $this->pending)
                        ->whereRelation('userRole', 'name', 'Vendor')->get();

        return response()->view('pages.users.kyc', [
            'users' => $requests
        ]);
    }

    function updateKycStatus(Request $request, NotificationService $notificationService, $user_id){
        $user = User::find($user_id);
        // ['confirmed', 'pending', 'declined']
        $user->kyc_status = $request->status;
        $user->save();

        if ($request->status === $this->declined) {
            $notificationService->subject("Your Account Request has been approved")
                                ->text('Congratulations, your '.env('APP_NAME').' account has been approved!')
                                ->text("You can now proceed to your application and enjoy the amazing benefits offered on the ".env('APP_NAME')." platform.")
                                ->send($user, ['mail']);
        }else{
            $notificationService->subject("Your Account Request has been declined")
                                ->text('Sorry, we could not approve your account verification request at this time because "'.$request->reason.'"')
                                ->text("Please update your information provided on your application and try again!")
                                ->text("You can reach out to our support center via ".env('SUPPORT_EMAIL'))
                                ->send($user, ['mail']);
        }


        return redirect()->back()->with('message', "User KYC Request has been $request->status");
    }
    public function update(UpdateUserRequest $request){
        $user = User::find($request->id);

        $update = $user->update($request->safe()->all());

        try{
            // update password if user input a new password
            // if(isset($request->password)){
            //     $update = $user->update([
            //         'password' => Hash::make($request->password)
            //     ]);
            // }

            return redirect()->back()->with('success', 'User information updated succesfully!');
        }catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);

        }
    }


    public function delete($id){
        $user   = User::find($id);
        if(!$user) return redirect('users')->with('error', 'User not found');

        $user->delete();
        return redirect('users')->with('success', 'User removed!');
    }
}
