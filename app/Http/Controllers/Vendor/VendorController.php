<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role\AccountRole;
use Carbon\Carbon;

use RealRashid\SweetAlert\Facades\Alert;

class VendorController extends Controller
{
    //
    protected function vendorsInterface($startDate = null, $endDate = null){
        $role = AccountRole::where('name', 'vendor')->first();
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
        $vendor = User::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);
        $payload = [
            'vendor' => $vendor,
        ];
        return view('pages.users.vendor-interface', $payload);
    }

    protected function getVendorsByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('vendor/interface/'.$startDate.'/'.$endDate);
    }

    protected function deleteUser(Request $request){
        $user = User::where('unique_id', $request->unique_id)->first();
        if($user == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'User'));
            return redirect()->back();
        }
        $user->delete();
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'User'));
        return redirect()->back();
    }

    protected function activateUserStatus(Request $request){
        $user = User::where('unique_id', $request->unique_id)->first();
        if($user == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'User'));
            return redirect()->back();
        }
        if($request->status == 'block'){
            $user->update(['status' => $this->pending]);
        }else{
            $user->update(['status' => $this->confirmed]);
        }
        Alert::success('Success', $this->returnSuccessMessage('updated', 'User'));
        return redirect()->back();
    }
}
