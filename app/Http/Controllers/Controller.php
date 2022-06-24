<?php

namespace App\Http\Controllers;

use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Traits\PaymentHandler;
use App\Traits\Options;
use App\Traits\UpdateAfterPayments;
use App\Traits\VerifyTwoFactor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ReturnTemplate, Generics,  FileUpload, PaymentHandler, Options, UpdateAfterPayments, VerifyTwoFactor;

    protected $notification;

    function __construct(NotificationService $notificationService)
    {
        $this->notification = $notificationService;
    }

    protected function notification()
    {
        $notification = new NotificationService();
        return $notification;
    }

    protected function user (){
        $user = Auth::user();
        return $user ? User::findOrFail($user->unique_id) : null;
    }

    protected function getSiteSettings(){
        $settings = new SiteSettings();
        return $settings->getSettings();
    }

    public function verifyPayment(Request $request)
    {
        $searchQuery = $request->query();
        $response = $this->handleGatewayCallback($searchQuery['reference']);
        if($response['status'] == true){
            $data = $response['data'];
            //updatetransaction table
            $transaction = $this->updateTransaction($data);
            if($transaction['type'] == 'vendor_subscription'){
                //update subscription status to comfirm
                $this->updateSubscribeVendorModel($data);               
            }elseif($transaction['type'] == 'fund_wallet'){
                //fund user wallet
                $this->updateUserMainWallet($data);
            }else{
                return $transaction;
            }
        }else{
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('payment_not_complete'));
        }
    }

    public function resend2faCode()
    {
        $data = $this->user()->generateCode();

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $data['code']);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }
}
