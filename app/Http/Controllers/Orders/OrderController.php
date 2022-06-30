<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Models\Address\Address;
use App\Models\Card;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Traits\PaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller {

    function create(CreateOrderRequest $request, OrderService $orderService){
        $user = $this->user();

        if(!User::find($request->courier_id)->isLogistic())
                    return $this->returnMessageTemplate(false, "The Selected Courier is not registered"); //

        if(!User::find($request->vendor_id)->isVendor())
                    return $this->returnMessageTemplate(false, "The Selected Vendor is not registered"); //

        $meals = $orderService->confirmMeals($request->meals); // Confirm the meals and prices
        $address = $request->address_id ? Address::find($request->address_id)->location : $request->receiver_location; // Handle Address

        // Calculate Delivery fee per
        $delivery_fee = $request->delivery_distance ? env('PRICE_PER_KM') * $request->delivery_distance : $request->delivery_fee;

        $order = Order::create($request->safe()->merge([
            'user_id' => $user->unique_id,
            'vendor_id' => $request->vendor_id,
            'unique_id' => $this->createUniqueId('orders'),
            'meals' => $meals,
            'receiver_name' => $request->receiver_name ?? $user->name,
            'receiver_phone' => $request->receiver_phone ?? $user->phone,
            'receiver_location' => $address,
            'amount' => $meals->sum('price'), //Calculate the price by sum
            'delivery_fee' => $delivery_fee
        ])->except('address_id')); // Create the order

        $transaction = $orderService->createOrderTransaction($order, $request); // Create Transaction for this Order

        if($request->payment_method == 'wallet'){
            //Payment With Wallet
            $response = $orderService->handleWalletPayment($order, $user, $transaction);
            if (!$response['status']) return $this->returnMessageTemplate(false, $response['message']);
            return $orderService->completeOrder($order, $transaction, $user);
        }elseif($request->payment_method == 'card' && $request->card_id){
            // Payment with existing card
            $response = $orderService->handleExistingCardPayment($request->card_id, $order, $user, $transaction);
            if (!$response['status']) return $this->returnMessageTemplate(false, $response['message']);
            return $orderService->completeOrder($order, $transaction, $user);
        }elseif ($request->payment_method == 'card' && !$request->card_id) {
            // Payment with a new Card
            $response = $orderService->initializePayment($order, $user, $transaction);
            return $response['status'] ? $response['data']['authorization_url'] : $this->returnMessageTemplate(false, $response['message']);
        }
    }

    function updateOrderStatus(Request $request) {
        // cancelled | declined | processing | enroute | pickup | delivered
    }

    function list(Request $request, $user_id){
        $user = User::find($user_id);
        $authUser = $this->user();

        if($user->isLogistic()){
            if(!$authUser->isLogistic()) abort(401);
            $orders = Order::where('courier_id', $user_id)->get();
        }else if ($user->isVendor()) {
            if(!$authUser->isVendor()) abort(401);
            $orders = Order::where('vendor_id', $user_id)->get();
        }else if($user->isUser()){
            if(!$authUser->isUser() && $authUser->unique_id === $user->unique_id) abort(401);
            $orders = Order::where('user_id', $user_id)->get();
        }

        return $this->returnMessageTemplate(true, '', $orders);
    }
}
