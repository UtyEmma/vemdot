<?php

namespace App\Traits;
use App\Models\User;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use App\Traits\ReturnTemplate;
use App\Models\Transaction\Transaction;

trait UpdateAfterPayments {
    use ReturnTemplate;

    public function updateTransaction($data){
        $transaction = null;
        $amount = $data['amount'] / 100;
        $transaction = Transaction::where('reference', $data['reference'])
        ->where($data['gateway_response'], 'Successful')
        ->where($data['status'], $this->success)
        ->where('amount', $amount)
        ->first();
        if($transaction != null){
            $transaction->channel = $data['channel'];
            $transaction->status = $this->comfirmed;
            $transaction->save();
        }
        return $transaction;
    }

    public function updateSubscribeVendorModel($data){
        $subscription = Subscription::where('unique_id', $data['reference'])->first();
        if($subscription != null){
            $subscription->status = $this->inprogress;
            $subscription->start_date = Carbon::now()->toDateTimeString();
            $subscription->save();
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Subscription Status'));
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }

    public function updateUserMainWallet($data){
        $user = User::where('unique_id', $data['reference'])->first();
        if($user != null){
            $amount = $data['amount'] / 100;
            $user->main_balance = $user->main_balance + $amount;
            $user->save();
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Main Balance'));
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }
}