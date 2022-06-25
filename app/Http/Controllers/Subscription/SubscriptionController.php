<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan\SubscriptionPlan;
use App\Models\Meal\Meal;
use App\Models\Transaction\Transaction;
use App\Models\Subscription\Subscription;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    //

    public function createVendorSubscription(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'plan_id'  => 'required',
            'meal_id'  => 'required',
            'payment_type'  => 'required',
        ]);

        if($validator->fails()) {
            return $this->returnMessageTemplate(false, $validator->messages());
        }

        $plan = SubscriptionPlan::where('unique_id', $data['plan_id'])
        ->where('status', '!=', $this->pending)
        ->first();
        if($plan == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Subscription Plan"));
        }

        $meal = Meal::where('unique_id', $data['meal_id'])
                        ->where('availability', $this->yes)
                        ->first();

        if($meal == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Meal"));
        }

        $reference = $this->createUniqueId('transactions');
        $orderID = $this->createRandomNumber(5);
        $description = $plan->name.' Subscription by '.$this->user()->name.' for '.$meal->name;

        if($data['payment_type'] == 'wallet'){
            if($this->user()->main_balance == 0 || $this->user()->main_balance < $plan->amount){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('insufficiant_fund'));
            }

            //create subscription record
            $sub = $this->createSubscription($reference, $plan, $meal, 'wallet');

            if($sub){
                //create transaction record
                $this->createTransaction($plan, $reference, $orderID, $description, 'wallet', null);
                //deduct the amount from the user balance
                $user = $this->user();
                $user->main_balance = ($user->main_balance - $plan->amount);
                $user->save();

                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Subscription Status'));

            }else{
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('subscription_exist'));
            }
        }else{
            $data = [
                "amount" => $plan->amount * 100,
                "reference" => $reference,
                "email" => $this->user()->email,
                "currency" => "NGN",
                "orderID" => $orderID,
                "description" => $description,
            ];

            //throw request for payment initialization
            $payment = $this->redirectToGateway($data);
            if($payment['status'] == true){
                //create subscription record
                $sub = $this->createSubscription($reference, $plan, $meal);
                if($sub){
                    //create transaction record
                    $this->createTransaction($plan, $reference, $orderID, $description, null,  $payment['data']);
                    return $payment['data']['authorization_url']; //local / testing
                    return Redirect::to($payment['data']['authorization_url']); //live / deployment
                }else{
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('subscription_exist'));
                }
            }else{
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('unable_to_pay'));
            }
        }
    }

    public function createTransaction($plan, $reference, $orderID, $description, $channel, $payment = null){
        Transaction::create([
            "unique_id" => $this->createUniqueId('transactions'),
            'user_id' => $this->user()->unique_id,
            'type' => 'vendor_subscription',
            'amount' => $plan->amount,
            'reference' => ($payment == null) ? $reference : $payment['reference'],
            'access_code' => ($payment == null) ? null : $payment['access_code'],
            'orderID' => $orderID,
            'description' => $description,
            'channel' => $channel,
            'status' => ($channel == 'wallet') ? $this->comfirmed : $this->pending,
        ]);
    }

    public function createSubscription($reference, $plan, $meal, $channel = null){
        $subscription = Subscription::where('user_id', $this->user()->unique_id)
                                    ->where('plan_id', $plan->unique_id)
                                    ->where('meal_id', $meal->unique_id)
                                    ->where('status', $this->inprogress)
                                    ->first();

        if($subscription == null){
            Subscription::create([
                'unique_id' => $reference,
                'user_id' => $this->user()->unique_id,
                'plan_id' => $plan->unique_id,
                'meal_id' => $meal->unique_id,
                'status' => ($channel == null) ? $this->pending : $this->inprogress,
                'start_date' => ($channel == null) ? null : Carbon::now()->toDateTimeString(),
            ]);

            return true;
        }else{
            return false;
        }
    }
}

