<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PushNotificationService {

    function send ($order, $user){
        $url = "https://fcm.googleapis.com/v1/projects/new-project/messages:send";
        $token = $user->device_id;

        $notification = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "body" => "This is the Notification",
                    "title" => "This is the message from the Notificiation"
                ]
            ]
        ];

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer ".env("FIREBASE_KEY")
        ])->post($url, $notification);

        return $response;
    }



}
