<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan\SubscriptionPlan;

use RealRashid\SweetAlert\Facades\Alert;

class PlansController extends Controller
{
    //
    function __construct(SubscriptionPlan $subscriptionPlan){
        $this->subscriptionPlan = $subscriptionPlan;
    }

    public function showCreatePlanPage(){
        return view('pages.used.add-plan');
    }

    public function createPlan(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'  => 'required',
            'amount'  => 'required',
            'duration'  => 'required',
            'no_of_item'  => 'required',
        ]);
        if($validator->fails()) {
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $validator->messages());
            }else{
                Alert::error('Error', $validator->messages()->first());
                return redirect()->back();
            }
        }

        $plans = $this->subscriptionPlan->getSubscriptionPlan([
            ['name', $data['name']],
        ]);

        if($plans != null){
            $plans->name = $data['name'] ? $data['name'] : null;
            $plans->amount = $data['amount'] ? $data['amount'] : null;
            $plans->duration = $data['duration'] ? $data['duration'] : null;
            $plans->items = $data['no_of_item'] ? $data['no_of_item'] : null;
            $plans->description = $data['description'] ? $data['description'] : null;
            $plans->status = $this->active; 
            $plans->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'subscription_plan', $plans->thumbnail, 1280, 720); 
            $plans->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_updated'), $plans);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_updated'));
                return redirect()->to('view/plans');
            }
        }else{
            $plan = new SubscriptionPlan();
            $plan->unique_id = $this->createUniqueId('subscription_plans', 'unique_id');
            $plan->name = $data['name'];
            $plans->amount = $data['amount'];
            $plans->duration = $data['duration'];
            $plans->items = $data['no_of_item'];
            $plan->description = $data['description'];
            $plan->status = $this->active; 
            $plan->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'subscription_plan', 'default.png', 1280, 720); 
            $plan->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_creation'), $plan);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_creation'));
                return redirect()->to('view/plans');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
    }

    public function viewPlans(Request $request){
        $plans = $this->subscriptionPlan->paginateSubscriptionPlans($this->paginate, [
            ['status', $this->active],
        ]);

        $payload = [
            'plans' => $plans,
        ];

        if($request->wantsJson()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $plans);
        }else{
            return view('pages.used.view-plans-list', $payload);
        }
    }

    public function editPlan(Request $request, $unique_id = null){
        if($unique_id != null){

            $plans = $this->subscriptionPlan->getSubscriptionPlan([
                ['unique_id', $unique_id],
            ]);
    
            $payload = [
                'plans' => $plans,
            ];
    
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('data_returned'), $plans);
            }else{
                return view('pages.used.edit-plans', $payload);
            }
        }else{
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
            }else{
                Alert::error('Error', $this->returnErrorMessage('unknown_error'));
                return redirect()->back();
            }
        }
    }

    public function updatePlan(Request $request, $unique_id = null){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'  => 'required',
            'amount'  => 'required',
            'duration'  => 'required',
            'no_of_item'  => 'required',
        ]);
        if($validator->fails()) {
            if($request->wantsJson()){
                return $this->returnMessageTemplate(false, $validator->messages());
            }else{
                Alert::error('Error', $validator->messages()->first());
                return redirect()->back();
            }
        }

        $plans = $this->subscriptionPlan->getSubscriptionPlan([
            ['unique_id', $unique_id],
        ]);

        if($plans != null){
            $plans->name = $data['name'] ? $data['name'] : null;
            $plans->amount = $data['amount'] ? $data['amount'] : null;
            $plans->duration = $data['duration'] ? $data['duration'] : null;
            $plans->items = $data['no_of_item'] ? $data['no_of_item'] : null;
            $plans->description = $data['description'] ? $data['description'] : null;
            $plans->status = $this->active; 
            $plans->thumbnail = $this->uploadImageHandler($request, 'thumbnail', 'subscription_plan', $plans->thumbnail, 1280, 720); 
            $plans->save();
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_updated'), $plans);
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_updated'));
                return redirect()->to('view/plans');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->to('view/plans');
        }
    }

    public function deletePlan(Request $request){
        $data = $request->all();

        $plans = $this->subscriptionPlan->getSubscriptionPlan([
            ['unique_id', $data['unique_id']],
        ]);

        if($plans != null){
            
            $plans->delete();
            
            if($request->wantsJson()){
                return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_deleted'));
            }else{
                Alert::success('Success', $this->returnSuccessMessage('successful_deleted'));
                return redirect()->to('view/plans');
            }
        }

        if($request->wantsJson()){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }else{
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }
    }
}
