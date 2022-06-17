<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;

class MealsController extends Controller{
    use Generics, ReturnTemplate, FileUpload;

    function create(CreateMealRequest $request){
        $user = $this->user();
        $unique_id = $this->createUniqueId('meals');

        $meal = Meal::create($request->safe()->merge([
            'unique_id' => $unique_id
        ])->all());

        if($this->getSiteSettings()->send_basic_emails == 'yes'){
            // Send Notification
            $this->notification
                    ->subject('Meal Sent Successfully')
                    ->text("We are glad and pleased to have you on board, feel free to explore our platform and enjoy our services")
                    ->send($user, ['mail']);
        }

        return $this->returnMessageTemplate(true, "Your Meal was created Successfully!");
    }

    function update(CreateMealRequest $request, $meal_id){
        if(!$meal = Meal::find($meal_id))
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Meal'));

        $meal->update($request->safe()->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', "Meal"));
    }


}
