<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Requests\Api\Order\OrderStatusRequest;
use App\Models\Address\Address;
use App\Models\Card;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Services\OrderService;
use App\Traits\PaymentHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
     * ORDER PROCESS
     *
     * 1. User makes the order and pays for the order
     * 2. User can cancel the order in as much as the vendor has not accepted the order - the paid amount will be refunded to the user based on a fixed percentage by the admin.
     * 2. Vendor Accepts or Rejects the Order
     * 3. If accepted, the order status changes to processing or inprogress
     * 4. When the order is ready, the vendor can mark it as ready - an alert is sent to the rider that the order is ready for pickup
     * 5. The rider or logistics company can mark the order as picked up
     * 6. The rider or logistics company can mark the order as delivered
     *
     * Site Settings
     * permitted_daily_cancellations, order_charges, can_cancel_order, PRICE_PER_KM
     *
     * cancelled | declined | confirmed | inprogress | terminated | enroute | pickup | delivered
     *
     *
     * ///
     *
     * Order Reference
     * Email Address
*/

class OrderController extends Controller {

    function create(CreateOrderRequest $request, OrderService $orderService){
        $user = $this->user();

        $rider = User::with(['logistic', 'userRole'])->find($request->bike_id);

        if(!($rider && $rider->isRider())) return $this->returnMessageTemplate(false, "The Selected User is not registered as a Rider");
        if(!User::find($request->vendor_id)->isVendor()) return $this->returnMessageTemplate(false, "The Selected User is not registered as a Vendor");

        try {
            $meals = $orderService->confirmMeals($request->meals); // Confirm the meals and prices
        } catch (\Throwable $th) {
            return $this->returnMessageTemplate(false, $th->getMessage());
        }

        $address = $request->address_id ? Address::find($request->address_id)->location : $request->receiver_location; // Handle Address

        $settings = SiteSettings::first();

        // Calculate Delivery fee per Kilometer
        $delivery_fee = $settings->delivery_fee * $request->delivery_distance;
        // $avg_delivery_time = $request->delivery_distance * $settings->delivery_fee;
        $delivery_time = $meals->max('time');

        $reference = $this->createRandomNumber(6);

        $order = Order::create($request->safe()->merge([
            'user_id' => $user->unique_id,
            'vendor_id' => $request->vendor_id,
            'unique_id' => $this->createUniqueId('orders'),
            'meals' => $meals,
            'receiver_name' => $request->receiver_name ?? $user->name,
            'receiver_phone' => $request->receiver_phone ?? $user->phone,
            'receiver_location' => $address,
            'receiver_email' => $request->receiver_email ?? $user->email,
            'amount' => $meals->sum('price'), //Calculate the price by sum
            'delivery_fee' => $delivery_fee,
            'avg_time' => $delivery_time,
            'reference' => $reference,
            'courier_id' => $rider->logistic->unique_id
        ])->except('address_id')); // Create the order

        $transaction = $orderService->createOrderTransaction($order, $request); // Create Transaction for this Order

        return $orderService->initiatePayment($request, $transaction, $user, $order);
    }

    function update(OrderStatusRequest $request, OrderService $orderService, $order_id) {
        if(!$order = Order::find($order_id)) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'The Order'));

        $status = $request->status;

        if($order->status === $status) return $this->returnMessageTemplate(false, "Order Status is already set to ".ucfirst($status));

        $user = $this->user();

        $canCancel = $orderService->checkPreviousCancelledOrders($user, $status);
            if(!$canCancel[0]) return $this->returnMessageTemplate(false, $canCancel[1]);

        if(!$orderService->checkCorrectUser($user, $order, $status))
                return $this->returnMessageTemplate(false, "You cannot set this order to ".ucfirst($status));

        $notUpdateable = $orderService->canUpdate($user, $order, $status);

        if($notUpdateable)
            return $this->returnMessageTemplate(false,
                    "Order Update failed because because it is set to ".ucfirst($order->status), ['order' => $order]);

        $vendorCanUpdateDelivered = (($user->userRole->name === 'Vendor') && ($status === 'delivered') && ($order->delivery_method !== 'pickup'));

        if($vendorCanUpdateDelivered) return $this->returnMessageTemplate(false, "You cannot update this order's status to $status");

        $orderService->handleStatusUpdate($order, $user, $status);
        $order = Order::with(['orderStatus'])->find($order->unique_id);

        $orderService->sendOrderUpdateNotification($order);
        // $orderService->onOrderTermination();
        return $this->returnMessageTemplate(true, "Order Status has been updated successfully", $order);
    }

    function list($user_id = null){
        $user = User::find($user_id) ?? $this->user();
        $query = Order::query();

        $query->when($user->isLogistic(), function($query) use($user){
            $query->where('courier_id', $user->unique_id);
        });

        $query->when($user->isRider(), function($query) use($user){
            $query->where('courier_id', $user->unique_id);
        });

        $query->when($user->isVendor(), function($query) use($user) {
            $query->where('vendor_id', $user->unique_id);
        });

        $query->when($user->isUser(), function($query) use($user) {
            $query->where('user_id', $user->unique_id);
        });

        $query->with(['orderStatus', 'vendor', 'courier', 'bike', 'user']);

        return $this->returnMessageTemplate(true, '', $query->get());
    }

    function show($order_id){
        $order = Order::with(['orderStatus', 'vendor', 'courier', 'bike', 'user'])->findorFail($order_id);
        return $this->returnMessageTemplate(true, '', $order);
    }

    function mealOrders($meal_id){
        $order = Order::whereJsonContains('meals', ['meal_id' => $meal_id]);
        return response($order->get());
        return $this->returnMessageTemplate(true, '', $order);
    }
}
