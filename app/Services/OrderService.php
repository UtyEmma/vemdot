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
use App\Notifications\OrderStatusNotification;
use App\Traits\Generics;
use App\Traits\Options;
use App\Traits\PaymentHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;

class OrderService {
    use Generics, PaymentHandler, Options;

    function confirmMeals($items, $vendor_id){
        $items = collect($items);

        $meals = $items->map(function($meal) use($vendor_id) {
            if($model = Meal::find($meal['meal_id'])){
                if($model->availability === $this->yes && $model->user_id === $vendor_id){
                    $main_price = $model->price * $meal['qty'];
                    $price = $model->discount ? $this->percentageDiff($main_price, $model->discount) : $main_price;
                    $tax = ($main_price * ($model->tax / 100));

                    $item['meal_id'] = $model->unique_id;
                    $item['vendor_id'] = $model->vendor->unique_id;
                    $item['price'] = $price + $tax;
                    $item['original_price'] = $model->price;
                    $item['unit_price'] = ($price + $tax) / $meal['qty'];
                    $item['qty'] = $meal['qty'];
                    $item['time'] = $model->avg_time;
                    $item['name'] = $model->name;
                    $item['thumbnail'] = $model->thumbnail;
                    $item['discount'] = $model->discount;
                    $item['tax'] = $model->tax;
                    return $item;
                }
            }

            throw new Exception('Invalid Meal Selected', 400);
        });

        return collect($meals);
    }

    function createOrderTransaction(Order $order, Request $request){
        return Transaction::create([
            'type' => 'order',
            'amount' => $order->amount + $order->delivery_fee,
            'orderID' => $order->unique_id,
            'save_card' => $request->save_card,
            'status' => ($request->payment_method == 'wallet') ? $this->confirmed : $this->pending,
            'reference' => "VM-".$this->createUniqueId('transactions', 'reference'),
            'channel' => $request->payment_method,
            "unique_id" => $this->createUniqueId('transactions'),
            'description' => "Payment for Meal Ordered on ".env('APP_NAME'),
            'user_id' => $request->user()->unique_id
        ]);
    }

    function handleWalletPayment(Order $order, User $user, Transaction $transaction){
        $wallet_amount = $user->main_balance + $user->ref_balance;
        $amount = $transaction->amount;
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
            "amount" => $transaction->amount * 100,
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
            "amount" => $transaction->amount * 100,
            "reference" => $transaction->reference,
            "currency" => "NGN",
            "email" => $user->email,
            "authorization_code" => $card->auth_code
        ]);

        if(!$payment['status']) return [false, $this->returnErrorMessage('unknown_error')];

        $data = $payment['data'];

        if(!($data['status'] === 'success' && $data['gateway_response'] === 'Approved')) return [false, $this->returnErrorMessage('unknown_error')];

        return  [true, ''];
    }

    function completeOrder(Order $order, Transaction $transaction, User $user){
        // dd($order);
        $transaction->status = $this->confirmed;
        $transaction->save();

        $notification = new NotificationService();

        $order->status = $this->paid;
        $order->transaction_id = $transaction->unique_id;
        $order->save();

        $admin = User::admin();
        $admin->main_balance = $admin->main_balance + $order->amount;
        $admin->save();

        $order->vendor;
        $order->courier;
        $order->bike;

        try {
            $notification->subject("Your Order has been created. Here's your Receipt!")
                            ->text("Your order with reference <strong>$order->reference</strong> has been created successfully!")
                            ->text("Please click the button below to download the receipt for your order!")
                            ->action("Download Receipt", route('order.invoice', ['reference' => $order->reference]))
                            ->text('<small>Please Note that you will be required to present this receipt to the Courier if your delivery method is Home Delivery.</small>')
                            ->text("<small>If you are picking up your order yourself, please show this receipt to the Vendor to confirm your order!</small>")
                            ->send($user, ['mail']);
        } catch (\Throwable $th) {}

        return $this->returnMessageTemplate(true, "You Order has been Created", ['order' => $order]);
    }

    function sendOrderUpdateNotification(Order $order){
        $user = $order->user;
        $vendor = $order->vendor;
        $rider = $order->bike;
        $order->orderStatus;

        if($userMessages = $this->userMessages($order->status))
            $user->device_id ? Notification::send($user, new OrderStatusNotification($userMessages, ['order' => $order])) : null;
        if($vendorMessages = $this->userMessages($order->status))
            $vendor->device_id ? Notification::send($vendor, new OrderStatusNotification($vendorMessages, ['order' => $order])) : null;
        if($riderMessages = $this->userMessages($order->status))
            $rider->device_id ? Notification::send($rider, new OrderStatusNotification($riderMessages, ['order' => $order])) : null;
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

        $progress = $this->orderProgression;

        if($status === array_pop($progress)){
            $meals = $order->meals;

            foreach($meals as $meal){
                $meal = Meal::find($meal['meal_id']);
                $meal->total_orders = $meal->total_orders + 1;
                $meal->save();
            }

            $settings = SiteSettings::first();

            $vendor = $order->vendor;
            $vendor->pending_balance += $this->percentageDiff($order->amount, $settings->vendor_service_charge);
            $vendor->save();

            $logistics = $order->courier;
            $logistics->pending_balance += $this->percentageDiff($order->delivery_fee, $settings->logistics_service_charge);
            $logistics->save();
        }

        if($status === 'cancelled' || 'terminated' || 'declined' || 'returned') $this->refundToWallet($order, $user);
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
     * 2. If the existing order status is set to cancelled|declined|terminated|returned the update fails because the order process has been terminated
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

        if(!$isNextStatus) $isNextStatus =  (
            $status === 'processing' || $status === 'declined' && $order->status === 'paid')
            ||
            ($status == 'done' && $order->status == 'processing')
            ||
            ($status == 'delivered' && $order->status == 'pickedup' && $user->isRider() && $order->delivery_method == 'home')
            ||
            ($status == 'delivered' && $order->status == 'done' && $user->isVendor() && $order->delivery_method == 'pickup');

        return
            ($statusPos <= $orderStatusPos)
            ||
            $this->isCompleted($order->status)
            ||
            !$isNextStatus
            ||
            ($status === 'cancelled' && $this->checkDeliveryTime($order) && $user->isUser() && $this->isCompleted($order->status));
    }

    function isCompleted($status) {
        return in_array($status, ['cancelled', 'declined', 'terminated', 'returned', 'delivered']);
    }

    function checkDeliveryTime(Order $order){
        // Check if the current time has exceeded the set time
        // It starts counting after the Vendor has accepted the order
        $orderStatus = $order->orderStatus()->where('status', 'processing')->first();
        return Date::parse($orderStatus->created_at)->addMinutes($order->avg_time)->greaterThan(now());
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
