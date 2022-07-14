<?php

namespace App\Services;
use App\Models\Withdrawal\WalletWithdrawal;
use App\Models\Bank\BankDetail;

class Withdrawal {

    public function getUserBankDetails($uniqueID, $user){
        $bankDetails = BankDetail::where('unique_id', $uniqueID)->first();
        if($bankDetails == null){
            $bankDetails = BankDetail::where('user_id', $user->unique_id)->first();
        }
        if($bankDetails == null)
            return false;
        return $bankDetails;    
    }

    public function getWithdrawal($condition, $paginate){
        return WalletWithdrawal::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($paginate);    
    }

    function getSigleWithdrawal($uniqueID){
        $withdraw = WalletWithdrawal::where('unique_id', $uniqueID)->first();
        if($withdraw == null)
            return false;
        return $withdraw;
    }

    public function createWithdrawalRequest($request, $user){
        $withdraw = WalletWithdrawal::create([
            'unique_id' => $request->unique_id,
            'user_id' => $user->unique_id,
            'bank_id' => $request->bank ?? null,
            'type' => 'wallet withdrwal',
            'amount' => $request->amount ?? null,
            'payment_method' => $request->payment_method ?? null,
            'recipient_code' => $request->recipient_code ?? null,
            'integration' => $request->integration ?? null,
            'reference' => $request->reference ?? null,
            'transfer_code' => $request->transfer_code ?? null,
            'description' => $request->description ?? null,
        ]);
        return $withdraw;
    }

    public function updateWalletWithdrawal($request, $uniqueID, $walletWithdrwal){
        if($uniqueID == null)
            return false;
        $withdrawal = $this->getSigleWithdrawal($uniqueID);
        if(!$withdrawal)
            return false;
        $withdrawal->update([
            'payment_method' => $request->payment_method ?? $walletWithdrwal->payment_method,
            'recipient_code' => $request->recipient_code ?? $walletWithdrwal->recipient_code,
            'integration' => $request->integration ?? $walletWithdrwal->integration,
            'reference' => $request->reference ?? $walletWithdrwal->reference,
            'transfer_code' => $request->transfer_code ?? $walletWithdrwal->transfer_code,
            'description' => $request->description ?? $walletWithdrwal->description,
        ]);    
        return true;    
    }

    public function updateWalletWithdrawalStatus($uniqueID, $status){
        if($uniqueID == null)
            return false;
        $withdrawal = $this->getSigleWithdrawal($uniqueID);
        if(!$withdrawal)
            return false;
        $withdrawal->update([
            'status' => $status,
        ]);    
        return true;    
    }

    function deleteWalletWithdraw($request){
        $withdraw = $this->getSigleWithdrawal($request->unique_id);
        if(!$withdraw)
            return false;
        $withdraw->delete();
        return true;
    }

    function getTotalAmount($status){
        $amt = 0;
        $row = WalletWithdrawal::where('status', $status)
            ->get();
        foreach($row as $each_row){
            $amt += $each_row->amount;
        }
        return $amt;       
    }

}