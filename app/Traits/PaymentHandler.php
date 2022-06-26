<?php

namespace App\Traits;
use App\Traits\ReturnTemplate;
use Exception;
use Illuminate\Support\Facades\Http;

trait PaymentHandler {
    use ReturnTemplate;

    public $key = 'sk_test_e348e0dda59d216ce25dc0744bcada10935a3d42';

    public function redirectToGateway($data) {
        try{
            $response = Http::withHeaders([
                "Authorization" => "Bearer $this->key",
                "Content-Type" => "application/json",
            ])->post('https://api.paystack.co/transaction/initialize', $data);

            return json_decode($response, true);
        }catch(Exception $e) {
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('paystack_token'));
        }
    }

    public function handleGatewayCallback($ref) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->get('https://api.paystack.co/transaction/verify/'.$ref);

        return json_decode($response, true);
    }

    public function verifyUserAccount($data) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->get('https://api.paystack.co/bank/resolve', [
            'account_number' => $data['account_number'],
            'bank_code' => $data['bank_code'],
        ]);

        return json_decode($response, true);
    }

    function payWithExistingCard($data){

        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->post('https://api.paystack.co/transaction/charge_authorization', $data);

        return json_decode($response, true);
    }

}
