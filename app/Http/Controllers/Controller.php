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

    protected function user(){
        $user = Auth::user();
        return $user ? User::findOrFail($user->unique_id) : null;
    }

    protected function getSiteSettings(){
        $settings = new SiteSettings();
        return $settings->getSettings();
    }

    public function verifyPayment(Request $request){
        $searchQuery = $request->query();
        $response = $this->handleGatewayCallback($searchQuery['reference']);

        if($response['status'] == true){
            $data = $response['data'];
            $amount = $data['amount'] / 100;
            //updatetransaction table
            $transaction = $this->updateTransaction($data);

            if($transaction != null){

                if($transaction->save_card == $this->yes){
                    $this->saveCardInfo($data, $transaction);
                }

                if($transaction->type == 'vendor_subscription'){
                    //update subscription status to comfirm
                    $subscribeResponse = $this->updateSubscribeVendorModel($data);
                    if($subscribeResponse){
                        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Subscription Status'));
                    }
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
                }elseif($transaction->type == 'fund_wallet'){
                    //fund user wallet
                    $walletResponse = $this->updateUserMainWallet($data);
                    if($walletResponse){
                        //send notification to user
                        $notification = $this->notification();
                        $notification->subject("Your Wallet Was Sucessfully Funded")
                            ->text('Your Main Wallet was sucessfully funded with '.$amount.' NGN')
                            ->text('You can now use your main wallet to make payments')
                            ->text('Thank you for using our services')
                            ->text('Kindly contact us if you have any questions')
                            ->send($transaction->owner, ['mail', 'database']);
                        //return success message to user
                        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Main Balance'));
                    }
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
                }

                return $transaction;
            }

            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('payment_not_complete'));
    }

    public function resend2faCode(){
        $data = $this->user()->generateCode();

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $data['code']);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }

    // method for user logoutUser and delete token
    protected function logoutUser()
    {
        auth()->user()->tokens()->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_logout'));
    }
}
