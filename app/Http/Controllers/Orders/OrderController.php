<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Requests\Api\Order\OrderStatusRequest;
use App\Models\Address\Address;
use App\Models\Order;
use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

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
*/

class OrderController extends Controller {

    function create(CreateOrderRequest $request, OrderService $orderService){
        $user = $this->user();

        if(!User::find($request->courier_id)->isLogistic()) return $this->returnMessageTemplate(false, "The Selected Courier is not registered");
        if(!User::find($request->vendor_id)->isVendor()) return $this->returnMessageTemplate(false, "The Selected Vendor is not registered");

        $meals = $orderService->confirmMeals($request->meals); // Confirm the meals and prices
        $address = $request->address_id ? Address::find($request->address_id)->location : $request->receiver_location; // Handle Address

        $settings = SiteSettings::first();

        // Calculate Delivery fee per
        $delivery_fee = $request->delivery_distance ? $settings->delivery_fee * $request->delivery_distance : $request->delivery_fee;

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

    function list(Request $request, $user_id = null){
        $user = User::find($user_id) ?? $this->user();
        $query = Order::query();

        $query->when($user->isLogistic(), function($query) use($user){
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

    protected function getOnGoingOrder($startDate = null, $endDate = null){
        $options = ['cancelled', 'declined', 'terminated', 'failed', 'delivered'];
        $order = Order::whereNotIn('status', $options)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);

        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $order = Order::whereNotIn('status', $options)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<', $endDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        if($startDate != null){
            $order = Order::where('status', $startDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        //return $order;
        $payload = [
            'orders' => $order,
        ];
        return view('pages.order.ongoing-order', $payload);
    }
    protected function getOngoingOrderByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('orders/interface/'.$startDate.'/'.$endDate);
    }
    protected function getOngoingOrderByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('orders/interface/'.$type);
    }

    //get the list of order history
    protected function getOnFinishedOrder($startDate = null, $endDate = null){
        $options = ['paid', 'processing', 'done', 'enroute', 'pickedup'];
        $order = Order::whereNotIn('status', $options)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);

        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $order = Order::whereNotIn('status', $options)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<', $endDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        if($startDate != null){
            $order = Order::where('status', $startDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        //return $order;
        $payload = [
            'orders' => $order,
        ];
        return view('pages.order.order-history', $payload);
    }
    protected function getOrderByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('orders/history/interface/'.$startDate.'/'.$endDate);
    }
    protected function getOrderByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('orders/history/interface/'.$type);
    }

    protected function terminateOrder(OrderService $orderService, Request $request){
        $response = $orderService->updateOrderStatus($request->unique_id, $this->failed);
        if(!$response){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Order'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('updated', 'Order'));
        return redirect()->back();
    }
    
    protected function deleteOrder(OrderService $orderService, Request $request){
        $response = $orderService->deleteOrder($request->unique_id);
        if(!$response){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Order'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Order'));
        return redirect()->back();
    }
}
