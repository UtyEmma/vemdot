<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class TestController extends Controller {

    function testPush(Request $request, PushNotificationService $pushNotificationService){
        $notification = $pushNotificationService->send();
        return response($notification->body());
    }

}
