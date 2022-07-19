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
use App\Traits\PaymentHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class OrderController extends Controller {

    function create(CreateOrderRequest $request, OrderService $orderService){
        $user = $this->user();
        $isHomeDelivery = $request->filled("bike_id") && ($request->delivery_method === 'home');

        if($isHomeDelivery){
            $rider = User::with(['logistic', 'userRole'])->find($request->bike_id);
            if(!($rider && $rider->isRider()))
                return $this->returnMessageTemplate(false, "The Selected User is not registered as a Rider");
        }

        if(!User::find($request->vendor_id)->isVendor())
            return $this->returnMessageTemplate(false, "The Selected User is not registered as a Vendor");

        try {
            // Confirm the meals and prices
            $meals = $orderService->confirmMeals($request->meals, $request->vendor_id);
        } catch (\Throwable $th) {
            return $this->returnMessageTemplate(false, $th->getMessage());
        }

        $address = $request->address_id ? Address::find($request->address_id)->location : $request->receiver_location; // Handle Address

        $settings = SiteSettings::first();

        // Calculate Delivery fee per Kilometer
        $delivery_fee = $settings->delivery_fee * $request->delivery_distance;
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
            'courier_id' => $isHomeDelivery ? $rider->logistic->unique_id : null
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

        $vendorCannotUpdateDelivered = (($user->userRole->name === 'Vendor') && ($status === 'delivered') && ($order->delivery_method !== 'pickup'));

        if($vendorCannotUpdateDelivered) return $this->returnMessageTemplate(false, "You cannot update this order's status to $status");

        $orderService->handleStatusUpdate($order, $user, $status);
        $order = Order::with(['orderStatus'])->find($order->unique_id);

        $orderService->sendOrderUpdateNotification($order);
        return $this->returnMessageTemplate(true, "Order Status has been updated successfully", $order);
    }

    function list($user_id = null){
        $user = User::find($user_id) ?? $this->user();
        $query = Order::query();

        $query->when($user->isLogistic(), function($query) use($user){
            $query->where('courier_id', $user->unique_id);
        });

        $query->when($user->isRider(), function($query) use($user){
            $query->where([
                'bike_id' => $user->unique_id,
                'delivery_method' => 'home'
            ]);
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
        return $this->returnMessageTemplate(true, '', $order->get());
    }

    function downloadInvoice($reference) {
        $order = Order::where(['reference' => $reference])->first();
        $vendor = User::find($order->vendor_id);
        $user = User::find($order->user_id);
        $interval = CarbonInterval::minutes($order->avg_time);
        $avg_time = CarbonInterval::make($interval)->cascade()->forHumans(['short' => true]);

        $pdf = Pdf::loadView('emails.order-email', [
            'vendor' => $vendor,
            'user' => $user,
            'order' => $order,
            'date' => Date::parse($order->created_at)->format('jS, F Y'),
            'avg_time' => $avg_time
        ]);

        return $pdf->download();
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
