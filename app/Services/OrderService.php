<?php

namespace App\Services;

use App\Models\Address\Address;
use App\Models\Card;
use App\Models\Meal\Meal;
use App\Models\Order;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Traits\Generics;
use App\Traits\Options;
use App\Traits\PaymentHandler;
use Illuminate\Http\Request;

class OrderService {
    use Generics, PaymentHandler, Options;

    function confirmMeals($items){
        $items = collect($items);
        // dd($meals);
        $meals = $items->map(function($meal){
            $meal = json_decode($meal);
            $model = Meal::find($meal->meal_id)->with('vendor')->first();
            $price = $model->discount ? $this->percentageDiff($model->price, $model->discount) : $model->price;
            $item['meal_id'] = $model->unique_id;
            $item['vendor_id'] = $model->vendor->unique_id;
            $item['price'] = $price * $meal->qty;
            $item['unit_price'] = $price;
            $item['qty'] = $meal->qty;
            return $item;
        });

        return collect($meals);
    }

    function createOrderTransaction(Order $order, Request $request){
        return Transaction::create([
            'type' => 'order',
            'amount' => $order->amount,
            'orderID' => $order->unique_id,
            'save_card' => $request->save_card,
            'status' => ($request->payment_method == 'wallet') ? $this->confirmed : $this->pending,
            'reference' => $this->createUniqueId('transactions', 'reference'),
            'channel' => $request->payment_method,
            "unique_id" => $this->createUniqueId('transactions'),
            'description' => "Payment for Order",
            'user_id' => $request->user()->unique_id
        ]);
    }

    function handleWalletPayment(Order $order, User $user, Transaction $transaction){
        $wallet_amount = $user->sum('main_balance', 'ref_balance');
        // Check if the wallet (both Main balance and Ref balance) is enough to complete the payment
        if(!$wallet_amount >= $order->amount) return [
            'status' => false,
            'message' => "Your Wallet balance is insufficent to complete this transaction"
        ];

        // Charge the Referal Balance first, if it is enough, then charge the Ref Balance
        if($user->ref_balance >= $order->amount){
            $user->ref_balance -= $wallet_amount;
        }else{ // If the Ref balance is not enough, set the ref_balance to 0 and change the difference to the main balance
            $bal = $wallet_amount - $user->ref_balance;
            $user->ref_balance = 0;
            $user->main_balance -= $bal;
        }

        $user->save();

        return [
            'status' => true,
            'message' => ""
        ];
    }

    function initializePayment(Order $order, User $user, Transaction $transaction){
        $data = [
            "amount" => $order->amount * 100,
            "reference" => $transaction->reference,
            "email" => $user->email,
            "currency" => "NGN",
            "orderID" => $order->unique_id,
            "description" => $transaction->description,
        ];
        return $this->redirectToGateway($data);
    }

    function handleExistingCardPayment($card_id, Order $order, User $user, Transaction $transaction){
        $card = Card::find($card_id);

        $data = [
            "amount" => $order->amount * 100,
            "reference" => $transaction->reference,
            "currency" => "NGN",
            "email" => $user->email,
            "authorization_code" => $card->auth_code
        ];

        $payment = $this->payWithExistingCard($data);

        if(!$payment['status'])
                return [
                    'status' => false,
                    'message' => $this->returnErrorMessage('unknown_error')
                ];

        $data = $payment['data'];

        if($data['status'] === 'success' && $data['gateway_response'] === 'Approved'){
            return [
                'status' => true,
                'message' => ''
            ];
        }else{
            return [
                'status' => false,
                'message' => $this->returnErrorMessage('unknown_error')
            ];
        }
    }

    function completeOrder(Order $order, Transaction $transaction, User $user){
        $transaction->status = $this->confirmed;
        $transaction->save();

        $order->status = $this->paid;
        $order->save();

        return $this->returnMessageTemplate(true, "You Order has been Initiatied", [
            'order' => $order
        ]);
    }


}
