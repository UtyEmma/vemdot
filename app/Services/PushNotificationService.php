<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PushNotificationService {

    function send (){
        $notification = [
            "registration_ids" => "",
            "notification" => [
                "title" => "Affdghj",
                "body" => "Adv bh bjhb ygih uuho oijij i oijo",
            ]
        ];

        $url = env('FIREBASE_URL');
        $response = Http::withHeaders([
            "Authorization" => "key=".env("FIREBASE_KEY")
        ])->post($url, $notification);

        return $response;
    }

}
