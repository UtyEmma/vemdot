<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\PushNotificationService;
use App\Traits\FileUpload;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller {
    use FileUpload;

    function testPush(Request $request, PushNotificationService $pushNotificationService){
        $notification = $pushNotificationService->send("");
        return response($notification->body());
    }

    function testOrderReceipt($order_id){

        $order = Order::find($order_id);
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

        $store = Storage::put('avatars/invoice.pdf', $pdf);

        $pdf = $pdf->download('invoice.pdf');

        $file = Storage::get('avatars/invoice.pdf');

        $fileUrl = Storage::path('avatars/invoice.pdf');
        // $order
        $file = file($fileUrl);

        // $url = $this->uploadFile($fileUrl, 'files');
        // return $url;
    }

}
