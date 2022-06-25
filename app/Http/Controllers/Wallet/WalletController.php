<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\Redirect;

class WalletController extends Controller
{
    //

    public function fundUserWallet(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount'  => 'required',
            'payment_type'  => 'required',
        ]);
        if($validator->fails())  
            return $this->returnMessageTemplate(false, $validator->messages());

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
        //throw request for payment initialization
        $payment = $this->redirectToGateway($data);
        if($payment['status'] == true){
            Transaction::create([
                "unique_id" => $this->createUniqueId('transactions'),
                'user_id' => $this->user()->unique_id,
                'type' => 'fund_wallet',
                'amount' => $data['amount'] / 100,
                'reference' => $payment['data']['reference'],
                'access_code' => $payment['data']['access_code'],
                'orderID' => $orderID,
                'description' => $description,
            ]);
            if(env('APP_ENV') == 'local'){
                return $payment['data']['authorization_url']; //local / testing
            }
            return Redirect::to($payment['data']['authorization_url']); //live / deployment
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
    }
}
