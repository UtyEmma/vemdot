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

        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());

        $plan = SubscriptionPlan::where('unique_id', $data['plan_id'])
            ->where('status', '!=', $this->pending)
            ->first();
        if($plan == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Subscription Plan"));
        // if the number of meals is greater than the number of items for the plan, return error    
        $explodeMeal = explode(',', $data['meal_id']);
        if(count($explodeMeal) > $plan->items){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('meal_exceed_plan'));
        }
        // if the availibility is not yes, return error
        foreach($explodeMeal as $eachMeal){
            $meal = Meal::where('unique_id', $eachMeal)->first();
            if($meal != null){
                if($meal->availability != 'yes')
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('meal_not_available', $meal->name));
            }
        }
        $reference = $this->createUniqueId('transactions');
        $orderID = $this->createRandomNumber(5);
        $description = $plan->name.' Subscription by '.$this->user()->name;

        if($data['payment_type'] == 'wallet'){
            if($this->user()->main_balance == 0 || $this->user()->main_balance < $plan->amount)
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('insufficiant_fund'));
            //create subscription record
            $sub = $this->createSubscription($reference, $plan, $explodeMeal, 'wallet');
            if($sub){
                //create transaction record
                $this->createTransaction($plan, $reference, $orderID, $description, 'wallet', null);
                //deduct the amount from the user balance
                $user = $this->user();
                $user->main_balance = ($user->main_balance - $plan->amount);
                $user->save();
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Your Subscription Status'));
            }
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('subscription_exist'));
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
                $sub = $this->createSubscription($reference, $plan, $explodeMeal);
                if($sub){
                    //create transaction record
                    $this->createTransaction($plan, $reference, $orderID, $description, null,  $payment['data']);
                    if(env('APP_ENV') == 'local'){
                        return $payment['data']['authorization_url']; //local / testing
                    }
                    return Redirect::to($payment['data']['authorization_url']); //live / deployment
                }
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('subscription_exist'));
            }

            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unable_to_pay'));
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
            'status' => ($channel == 'wallet') ? $this->confirmed : $this->pending,
        ]);
    }

    public function createSubscription($reference, $plan, $explodeMeal, $channel = null){
        $subscription = Subscription::where('user_id', $this->user()->unique_id)
            ->where('plan_id', $plan->unique_id)
            ->where('status', $this->inprogress)
            ->first();
        if($subscription == null){
            Subscription::create([
                'unique_id' => $reference,
                'user_id' => $this->user()->unique_id,
                'plan_id' => $plan->unique_id,
                'meal_id' => $explodeMeal,
                'status' => ($channel == null) ? $this->pending : $this->inprogress,
                'start_date' => ($channel == null) ? null : Carbon::now()->toDateTimeString(),
            ]);
            return $this->updateMealPromotion($explodeMeal, $channel);
        }
        return false;
    }

    protected function updateMealPromotion($meal, $channel){
        if($channel == 'wallet'){
            foreach($meal as $eachMeal){
                Meal::where('unique_id', $eachMeal)->update(['promoted' => $this->yes]);
            }
        }
        return true;
    }
}

