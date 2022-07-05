<?php

namespace App\Http\Controllers\Withdrawal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank\BankDetail;
use Illuminate\Support\Facades\Validator;
use App\Services\Withdrawal;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class WithdrawalController extends Controller
{
    //
    protected function fetchUserBankDetails()
    {
        $bankDetails = BankDetail::where('user_id', $this->user()->unique_id)->get();
        if($bankDetails->count() > 0){
            foreach($bankDetails as $bank){
                $bank->bank;
            }
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Bank Details'), $bankDetails);
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('no_bank_details'));
    }
    protected function fetchSingleBankDetails(Withdrawal $withdrawal, $unique_id = null)
    {
        if($unique_id == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        $bankDetails = $withdrawal->getUserBankDetails($unique_id, $this->user());
        if($bankDetails == false)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Bank Detail'));

        $bankDetails->bank;
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Bank Detail'), $bankDetails);
    }
    /**
    * This function initiates the withdrawal method.
    * It checks if the withdrwal is set to automatic or manual and proceed as supposed
    * 
    */
    protected function initiateWithdrawal(Request $request, Withdrawal $withdraw){
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'amount'  => 'required',
            'bank'  => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());

        if($request->amount >= $user->main_balance)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('insufficiant_fund'));
        // get the user's bank details from the bank details table, if a wrong id is inserted, this service function fetches the logged in user's first record in the bank details table, but if none is set up. it throws an error
        $bankDetails = $withdraw->getUserBankDetails($request->bank, $user);
        if($bankDetails == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('no_bank_details'));
        //$balance = ($request->amount - $user->main_balance);
        $user->update([
            'main_balance' => $user->main_balance - $request->amount,
        ]);
        $request->unique_id = $this->createUniqueId('wallet_withdrawals');
        //create a new withdrawal
        $withdrawal = $withdraw->createWithdrawalRequest($request, $user);
        if(!$withdrawal)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        //send the user notification informing them of the successful withdrawal request    
        $notification = $this->notification();
        $notification->subject("Wallet Balance Withdrawal")
             ->text('you withdrawal request of '.$request->amount.'NGN')
             ->text('was successful, you account will be credited shortly')
             ->send($user, ['mail', 'database']);

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Wallet Withdrawal Request'), $withdrawal);
    }
    protected function withdrawalPayout(Request $request, Withdrawal $withdraw){
        $response = $withdraw->getSigleWithdrawal($request->unique_id);
        if(!$response){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Withdraw Request'));
            return redirect()->back();
        }
        $response->bankDetails;
        $response->bankDetails->bank;

        $data = [
            'type' => 'nuban',
            'name' => $response->bankDetails->account_name,
            'account_number' => $response->bankDetails->account_no,
            'bank_code' => $response->bankDetails->bank->code,
            'currency' => 'NGN',
        ];
        //Creates a new recipient. A duplicate account number will lead to the retrieval of the existing record.
        $responses = $this->transferRecipient($data);
        if(!$responses['status']){
            Alert::error('Error', $responses['message']);
            return redirect()->back();
        }
        //update the recipient code for the payout
        $response->update([
            'recipient_code' => $responses['data']['recipient_code'],
            'integration' => $responses['data']['integration'],
        ]);
        $data = [
            "source" => "balance", 
            "reason" => $response->description,
            "amount" => $response->amount * 100, 
            "recipient" => $responses['data']['recipient_code'],
        ];
        // Initiate Transfer
        $transfer = $this->initiateTransfer($data);
        if(!$transfer['status']){
            Alert::error('Error', $transfer['message']);
            return redirect()->back();
        }
        if($transfer['data']['status'] == 'otp'){
            Alert::success('Success', $transfer['message']);
            return redirect()->to('withdrawal/otp/interface/'.$transfer['data']['transfer_code'].'/'.$response->unique_id);
        }
        //update the transfer code for the payout
        $response->update([
            'transfer_code' => $transfer['data']['transfer_code'],
            'status' => $this->confirmed,
        ]);
        Alert::success('Success', $this->returnSuccessMessage('payout'));
        return redirect()->back();      
    }
    protected function processOTPInterface($transfer_code = null, $uniqueId = null){
        if($transfer_code == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
        $payload = [
            'transfer_code' => $transfer_code, 
            'uniqueId' => $uniqueId
        ];
        return view('pages.withdrawal.process-otp', $payload);
    }
    protected function processOTP(Request $request, Withdrawal $withdraw){
        $validator = Validator::make($request->all(), [
            'otp'  => 'required|numeric',
            'transfer_code'  => 'required',
            'uniqueId'  => 'required',
        ]);
        if($validator->fails()){
            Alert::error('Error', $validator->messages()->first());
            return redirect()->back();
        }
        $withdrawal = $withdraw->getSigleWithdrawal($request->uniqueId);
        if(!$withdrawal){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
        $data = [
            "transfer_code" => $request->transfer_code, 
            "otp" => $request->otp, 
        ];
        $response = $this->finalizeTransfer($data);
        if(!$response['status']){
            Alert::error('Error', $response['message']);
            return redirect()->back();
        }
        $withdrawal->update([
            'transfer_code' => $response['data']['transfer_code'],
            'reference' => $response['data']['reference'],
            'status' => $this->confirmed,
        ]);
        Alert::success('Success', $this->returnSuccessMessage('payout'));
        return redirect()->to('withdrawal/interface');
    }
    /**
    * This function fetches all the withdrawal request for the admin / super admin.
    * 
    */
    protected function withdrawalRequestInterface(Withdrawal $withdraw, $startDate = null, $endDate = null){
        $condition = [
            ['status', $this->pending]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        $payload = [
            'withdrawals' => $withdraw->getWithdrawal($condition, $this->paginate),
        ];
        return view('pages.withdrawal.withdrawal-request', $payload);
    }
    protected function getWithdrawalalRequestByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('withdrawal/interface/'.$startDate.'/'.$endDate);
    }
    protected function getWithdrawalalRequestByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('withdrawal/interface/'.$type);
    }
    /**
    * This function fetches all the withdrawals for the admin / super admin.
    * 
    */
    protected function withdrawalInterface(Withdrawal $withdraw, $startDate = null, $endDate = null){
        $condition = [
            ['status', '!=', $this->pending]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        $payload = [
            'withdrawals' => $withdraw->getWithdrawal($condition, $this->paginate),
            'pendingAmount' => $withdraw->getTotalAmount($this->pending),
            'failedAmount' => $withdraw->getTotalAmount($this->failed),
            'confirmedAmount' => $withdraw->getTotalAmount($this->confirmed),
        ];
        return view('pages.withdrawal.withdrawal-history', $payload);
    }
    protected function getWithdrawalalByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('withdrawal/histroy/interface/'.$startDate.'/'.$endDate);
    }
    protected function getWithdrawalalByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('withdrawal/histroy/interface/'.$type);
    }
    protected function deleteWithdrawal(Withdrawal $withdraw, Request $request){
        $withdraw = $withdraw->deleteWalletWithdraw($request);
        if(!$withdraw){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Withdraw Request'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Withdraw Request'));
        return redirect()->back();
    }
    protected function declineWithdrawal(Withdrawal $withdraw, Request $request){
        $response = $withdraw->updateWalletWithdrawalStatus($request->unique_id, $this->failed);
        if(!$response){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Withdraw Request'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('updated', 'Withdraw Request'));
        return redirect()->back();
    }
}
