<?php

namespace App\Services;

use App\Models\Address\Address;
use App\Models\Card;
use App\Models\Meal\Meal;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Site\SiteSettings;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Traits\Generics;
use App\Traits\Options;
use App\Traits\PaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

class OrderService {
    use Generics, PaymentHandler, Options;

    function confirmMeals($items){
        $items = collect($items);
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
        $amount = $order->amount + $order->delivery_fee;
        // Check if the wallet (both Main balance and Ref balance) is enough to complete the payment
        if(!$wallet_amount >= $amount) return [false, "Your Wallet balance is insufficent to complete this transaction"];

        // Charge the Referal Balance first, if it is not enough, then charge the Ref Balance
        if($user->ref_balance >= $amount){
            $user->ref_balance -= $wallet_amount;
        }else{ // If the Ref balance is not enough, set the ref_balance to 0 and change the difference to the main balance
            $bal = $wallet_amount - $user->ref_balance;
            $user->ref_balance = 0;
            $user->main_balance -= $bal;
        }

        $user->save();
        return [true, ""];
    }

    function initializePayment(Order $order, User $user, Transaction $transaction){
        return $this->redirectToGateway([
            "amount" => $order->amount * 100,
            "reference" => $transaction->reference,
            "email" => $user->email,
            "currency" => "NGN",
            "orderID" => $order->unique_id,
            "description" => $transaction->description,
        ]);
    }

    function handleExistingCardPayment($card_id, Order $order, User $user, Transaction $transaction){
        if(!$card = Card::find($card_id)) return [false, $this->returnErrorMessage('not_found', 'The Selected')];

        $payment = $this->payWithExistingCard([
            "amount" => $order->amount * 100,
            "reference" => $transaction->reference,
            "currency" => "NGN",
            "email" => $user->email,
            "authorization_code" => $card->auth_code
        ]);

        if(!$payment['status']) return [false, $this->returnErrorMessage('unknown_error')];

        $data = $payment['data'];

        if(!($data['status'] === 'success' && $data['gateway_response'] === 'Approved'))
                    return [false, $this->returnErrorMessage('unknown_error')];

        return  [true, ''];
    }

    function completeOrder(Order $order, Transaction $transaction, User $user){
        $transaction->status = $this->confirmed;
        $transaction->save();
        $order->status = $this->paid;
        $order->save();

        $settings = SiteSettings::first();

        $admin = User::admin();
        $admin->main_balance += $order->amount;
        $admin->save();

        $vendor = $order->vendor;
        $vendor->pending_balance += $this->percentageDiff($order->amount, $settings->vendor_service_charge);
        $vendor->save();

        $logistics = $order->courier;
        $logistics->pending_balance += $this->percentageDiff($order->delivery_fee, $settings->logistics_service_charge);

        return $this->returnMessageTemplate(true, "You Order has been Created", ['order' => $order]);
    }

    function sendOrderUpdateNotification(Order $order){

    }

    function onOrderTermination(){

    }

    function handleStatusUpdate(Order $order, User $user, $status){
        $order->update([
            'status' => $status
        ]);

        OrderStatus::create([
            'unique_id' => $this->createUniqueId('order_statuses'),
            'order_id' => $order->unique_id,
            'status' => $order->status
        ]);

        if($status === 'cancelled' || 'terminated' || 'declined') $this->refundToWallet($order, $user);
        return $order->refresh();
    }

    function refundToWallet(Order $order, User $user){
        $settings = SiteSettings::first();
        $amount = $order->amount + $order->delivery_fee;

        $refund = $settings->charge_cancellations == $this->yes ? $this->percentageDiff($amount, $settings->cancellation_fee) : $amount;

        $user->main_balance += $refund;
        $user->save();

        $admin = User::admin();
        $admin->main_balance -= $refund;

        if($admin->main_balance < 0) $admin->main_balance = 0;
        $admin->save();
    }

    function checkPreviousCancelledOrders(User $user, $status){
        if (($status === 'cancelled' || 'terminated')) {
            $orders = Order::query();
            $settings = SiteSettings::first();

            $orders->when(($user->isVendor() && $status === 'terminated'), function($query){
                $query->where('status', 'terminated');
            });

            $orders->when($user->isUser() && $status === 'cancelled', function($query){
                $query->where('status', 'cancelled');
            });

            $cancelled_orders = $orders->whereDate('created_at', Date::today())->count();

            if($cancelled_orders >= $settings->cancellation_limit) return [false, 'You have exceeded your order cancellation limit for today!'];
        }

        return [true];
    }

    function checkCorrectUser(User $user, Order $order, $status){
        $actions = $this->orderUserActions;
        $role = $user->userRole->name;
        $canSetDelivery = ($order->delivery_method === 'pickup' && $status == 'delivered' && $role === 'Vendor');
        return in_array($status, $actions[$role]) || $canSetDelivery;
    }

    /**
     * LOGIC
     *
     * Order Progression array has the steps every order will take in the order which they will be taken
     * - canUpdate function checks to ensure that each status update falls in the correct progression
     *
     * These are the rules of this logic
     * 1. If the position (index) of an incoming status is below that of it's predecessor in the array, the update fails
     * 2. If the existing order status is set to cancelled|declined|terminated|failed the update fails because the order process has been terminated
     * 3. If the incoming Status is more than one step above it's predecessor, the update fails because there is an attempt to jump the process (hackers😊)
     * 4. The exclusion to rule 3 is in any of the following instances:
     *  a. Incoming Status is processing or declined and current status is paid
     *  b. Incoming Status is done and current status is processing
     *  c. Incoming Status is delivered and current status is done and delivery method is pickup and user is vendor
     */

    function canUpdate(User $user, Order $order, $status){
        $progression = $this->orderProgression;
        $statusPos = array_search($status, $progression);
        $orderStatusPos = array_search($order->status, $progression);
        $isNextStatus = $orderStatusPos + 1 === $statusPos;

        if(!$isNextStatus) $isNextStatus =  ($status === 'processing' || $status === 'declined' && $order->status === 'paid') || ($status == 'done' && $order->status == 'processing') || ($status == 'delivered' && $order->status == 'done' && $user->isVendor() && $order->delivery_method == 'pickup');

        return ($statusPos <= $orderStatusPos) || in_array($order->status, ['cancelled', 'declined', 'terminated', 'failed']) || !$isNextStatus;
    }

    function initiatePayment (Request $request, Transaction $transaction, User $user, Order $order){
        if($request->payment_method == 'wallet'){
            $response = $this->handleWalletPayment($order, $user, $transaction);
        }elseif($request->payment_method == 'card' && $request->card_id){
            $response = $this->handleExistingCardPayment($request->card_id, $order, $user, $transaction);
        }elseif ($request->payment_method == 'card' && !$request->card_id) {
            $response = $this->initializePayment($order, $user, $transaction);
            return $response['data']['authorization_url'];
        }

        if ($response[0]) return $this->completeOrder($order, $transaction, $user);

        return $this->returnMessageTemplate(false, $response[1]);
    }

    function getAllOrders($condition, $paginate){
        return Order::where($condition)
            ->orderBy('id', 'desc')
            ->paginate($paginate);
    }

    function getSingleOrder($uniqueID){
        return Order::where('unique_id', $uniqueID)->first();
    }

    public function updateOrderStatus($uniqueID, $status){
        $order = $this->getSingleOrder($uniqueID);
        if(!$order)
            return false;
        return $order->update(['status' => $status]);  
    }

    public function deleteOrder($uniqueID){
        $order = $this->getSingleOrder($uniqueID);
        if(!$order)
            return false;
        return $order->delete();  
    }
}
