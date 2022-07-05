<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Users\UpdateUserRequest;
use Illuminate\Http\Request;

use App\Models\User;
use App\Services\NotificationService;
use App\Models\Role\AccountRole;
use App\Models\Country\CountryList;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index($startDate = null, $endDate = null){
        $role = AccountRole::where('name', 'User')->first();
        if($role == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
        $condition = [
            ['role', $role->unique_id]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        $users = User::where($condition)
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.users.index', [
            'users' => $users
        ]);
    }

    protected function getUserByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('users/view/'.$startDate.'/'.$endDate);
    }

    public function edit($user_id){
        if(!$user = User::where('unique_id', $user_id)->with('userRole')->first())
            return redirect()->back('error', $this->returnErrorMessage('not_found', "User"));
        $country = CountryList::all();
        return view('pages.users.profile', [
            'user' => $user,
            'country' => $country,
        ]);
    }

    protected function updateUser(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|between:2,100',
            'phone' => 'required',
            'country' => 'required',
        ]);
        if($validator->fails()){
            Alert::error('Error', $validator->messages());
            return redirect()->back();
        }

        $user = User::find($request->user_id);
        if($user == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'User'));
            return redirect()->back();
        }

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'country' => $request->country,
            'gender' => $request->gender,
            'business_name' => $request->business_name,
            'city' => $request->city,
            'state' => $request->state,
            'address' => $request->address,
        ]);

        Alert::success('Success', $this->returnSuccessMessage('updated', 'User'));
        return redirect()->back();
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
