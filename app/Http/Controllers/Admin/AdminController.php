<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country\CountryList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Role\AccountRole;
use Carbon\Carbon;

class AdminController extends Controller
{
    //
    protected function createAdminInterface(){
        $country = CountryList::all();
        return view('pages.users.create-admin', [
            'country'=>$country,
        ]);
    }

    protected function createAdminRequest(Request $request){
        $data = $request->all();
        $user = $request->user();
        $validator = Validator::make($data, [
            'name' => 'required|between:2,100',
            'email' => 'required|email|unique:users|max:50',
            'password' => ['required', Rules\Password::defaults()],
        ]);
        if($validator->fails()){
            Alert::error('Error', $validator->messages());
            return redirect()->back();
        }
        if($user->userRole->name != 'Super Admin'){
            Alert::error('Error', $this->returnErrorMessage('not_authorized'));
            return redirect()->back();
        }

        $role = AccountRole::where('name', 'Admin')->first();
        $admin = User::create([
            'unique_id' =>  $this->createUniqueId('users'),
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $role->unique_id,
            'country' => $data['country'],
            'phone' => $data['phone'],
            'referral_id' => $this->createUniqueId('users', 'referral_id'),
            'password' => Hash::make($data['password']),
            'status' => $this->confirmed,
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'two_factor' => 'yes',
            'referred_id' => $user->referral_id,
        ]);

        //notify logistic about new bike/ride
        $notification = $this->notification();
        $notification->subject('New Admin Was Added')
            ->text('A new admin was successfully added.')
            ->text('Below are the details of the new admin:')
            ->text('Admin Name: '.$data['name'])
            ->text('Admin Email: '.$data['email'])
            ->text('Admin Password: '.$data['password'])
            ->send($admin, ['mail', 'database']);
           
        Alert::success('Success', $this->returnSuccessMessage('created', 'Admin'));
        return redirect()->back();
    }

    protected function fetchAdminInterface($startDate = null, $endDate = null){
        if($this->user()->userRole->name != 'Super Admin'){
            Alert::error('Error', $this->returnErrorMessage('not_authorized'));
            return redirect()->back();
        }
        $role = AccountRole::where('name', 'Admin')->first();
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
       // return $condition;
        $admin = User::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);
        $payload = [
            'admin' => $admin,
        ];
        return view('pages.users.admins-interface', $payload);
    }

    protected function getAdminByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('admin/view/interface/'.$startDate.'/'.$endDate);
    }
}
