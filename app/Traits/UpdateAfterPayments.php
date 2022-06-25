<?php

namespace App\Traits;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use App\Traits\ReturnTemplate;
use App\Traits\Options;
use App\Models\Transaction\Transaction;

trait UpdateAfterPayments {
    use ReturnTemplate, Options;

    public function updateTransaction($data){
        $transaction = null;
        $amount = $data['amount'] / 100;
        $transaction = Transaction::where('reference', $data['reference'])
        ->where('amount', $amount)
        ->where('status', $this->pending)
        ->first();
        if($transaction != null){
            $transaction->update([
                'status' => $this->comfirmed,
                'channel' => $data['channel'],
            ]);
        }
        return $transaction;
    }

    public function updateSubscribeVendorModel($data){
        $subscription = Subscription::where('unique_id', $data['reference'])->first();
        if($subscription != null){
            $subscription->status = $this->inprogress;
            $subscription->start_date = Carbon::now()->toDateTimeString();
            $subscription->save();
            return true;
        }
        return false;
    }

    public function updateUserMainWallet($data){
        $amount = $data['amount'] / 100;
        $transaction = Transaction::where('reference', $data['reference'])
        ->where('amount', $amount)
        ->where('status', $this->comfirmed)
        ->where('type', 'fund_wallet')
        ->first();
        if($transaction != null){
            $transaction->update([
                'status' => $this->settled,
            ]);
            $user = $transaction->owner;
            $user->main_balance = $user->main_balance + $amount;
            $user->save();
            return true;
        }
        return false;
    }
}