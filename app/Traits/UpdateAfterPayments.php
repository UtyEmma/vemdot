<?php

namespace App\Traits;

use App\Models\Card;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use App\Traits\ReturnTemplate;
use App\Traits\Options;
use App\Models\Transaction\Transaction;
use App\Models\User;

trait UpdateAfterPayments {
    use ReturnTemplate, Options, Generics;

    public function updateTransaction($data){
        $amount = $data['amount'] / 100;
        $transaction = Transaction::where('reference', $data['reference'])
                                    ->where('amount', $amount)
                                    ->where('status', $this->pending)
                                    ->first();
        if($transaction !== null){
            $transaction->update([
                'status' => $this->confirmed,
                'channel' => $data['channel'],
            ]);
        }

        return $transaction;
    }

    public function saveCardInfo($payment_info, $transaction){
        $user = User::find($transaction['user_id']);
        $authorization = $payment_info['authorization'];
        $card = $user->cards()->where('signature', $authorization['signature'])->first();

        if(!$card){
            $card = Card::create([
                'unique_id' => $this->createUniqueId('cards'),
                'user_id' => $user->unique_id,
                'auth_code' => $authorization['authorization_code'],
                'signature' => $authorization['signature'],
                'data' => $authorization
            ]);
        }

        return $card;
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
                                    ->where('status', $this->confirmed)
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
