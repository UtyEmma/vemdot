<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class SendTextMessage {

    public $API_KEY = '788be92e';
    public $API_SECRET = 'Ih354eQPKgCtl5cL';

    function sendTextMessage($to, $from, $text){
        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'from' => $from,
            'text' => $text,
            'to' => $to,
            'api_key' => $this->API_KEY,
            'api_secret' => $this->API_SECRET,
        ]);

        return json_decode($response, true);
    }
}