<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction\Transaction;
use App\Traits\UpdateAfterPayments;
use Illuminate\Support\Facades\Redirect;

class WalletController extends Controller {
    use UpdateAfterPayments;

    public function fundUserWallet(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount'  => 'required',
            'payment_type'  => 'required',
            'save_card' => 'nullable|string|in:yes,no',
            'card_id' => 'nullable'
        ]);

        if($validator->fails()) return $this->returnMessageTemplate(false, $validator->messages());

        $reference = $this->createUniqueId('users');
        $orderID = $this->createRandomNumber(5);
        $description = 'Wallet Top Up by '.$this->user()->name;

        $data = [
            "amount" => $data['amount'] * 100,
            "reference" => $reference,
            "email" => $this->user()->email,
            "currency" => "NGN",
            "orderID" => $orderID,
            "description" => $description,
        ];

        if($request->has('card_id') && $request->filled('card_id')){
            if(!$card = Card::find($request->card_id))
                    return $this->returnMessageTemplate(false, $this->returnSuccessMessage('not_found', 'Card'));

                $data = [
                    "amount" => $request->amount * 100,
                    "reference" => $reference,
                    "currency" => "NGN",
                    "email" => $this->user()->email,
                    "authorization_code" => $card->auth_code
                ];

                $payment = $this->payWithExistingCard($data);

                if(!$payment['status'])
                        return $this->returnMessageTemplate(false,  $this->returnErrorMessage('unknown_error'));

                $data = $payment['data'];

                if($data['status'] === 'success' && $data['gateway_response'] === 'Approved'){
                    $transaction = Transaction::create([
                        "unique_id" => $this->createUniqueId('transactions'),
                        'user_id' => $this->user()->unique_id,
                        'type' => 'fund_wallet',
                        'amount' => $data['amount'] / 100,
                        'reference' => $payment['data']['reference'],
                        'access_code' => null,
                        'orderID' => $orderID,
                        'description' => $description,
                        'channel' => 'card',
                        'save_card' => $this->no,
                        'status' => $this->confirmed
                    ]);

                    $walletResponse = $this->updateUserMainWallet($data);

                    if($walletResponse){
                        //send notification to user
                        $notification = $this->notification();
                        $notification->subject("Your Wallet Was Sucessfully Funded")
                            ->text('Your Main Wallet was sucessfully funded with '.$transaction->amount.' NGN')
                            ->text('You can now use your main wallet to make payments')
                            ->text('Thank you for using our services')
                            ->text('Kindly contact us if you have any questions')
                            ->send($transaction->owner, ['mail', 'database']);
                        //return success message to user

                        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Main Balance'));
                    }
                }

                return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));

        }else{
            $payment = $this->redirectToGateway($data);
        }

        if($payment['status'] == true ){
            Transaction::create([
                "unique_id" => $this->createUniqueId('transactions'),
                'user_id' => $this->user()->unique_id,
                'type' => 'fund_wallet',
                'amount' => $data['amount'] / 100,
                'reference' => $payment['data']['reference'],
                'access_code' => $payment['data']['access_code'],
                'orderID' => $orderID,
                'description' => $description,
                'save_card' => $request->save_card ?? $this->no
            ]);

            if(env('APP_ENV') == 'local'){
                return $payment['data']['authorization_url']; //local / testing
            }

            return Redirect::to($payment['data']['authorization_url']); //live / deployment
        }

        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }
}
