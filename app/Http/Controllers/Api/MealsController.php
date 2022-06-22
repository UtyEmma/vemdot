<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\User;
use App\Services\MealService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class MealsController extends Controller{

    function __construct(MealService $mealService){
        $this->mealService = $mealService;
    }

    function create(CreateMealRequest $request){
        $user = $this->user();

        if(!MealCategory::find($request->category)) return abort(400, "The Selected Meal Category Does not Exist");

        $unique_id = $this->createUniqueId('meals');

        $meal = Meal::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'user_id' => $user->unique_id
        ])->all());

        return $this->returnMessageTemplate(true, "Your Meal was created Successfully!", [
            'meal' => $meal
        ]);
    }

    function update(CreateMealRequest $request, $meal_id){
        $meal = Meal::findOrFail($meal_id);
        $meal->update($request->safe()->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', "Meal"), [
            'meal' => $meal
        ]);
    }

    function vendorMeals(MealService $mealService, $vendor_id = null){
        $user = $this->user();
        if($user->userRole->name === 'Vendor') {
            $meals = $mealService
                    ->byUser($user->unique_id)
                    ->category()
                    ->status()
                    ->query();
        }else{
            if($vendor_id){
                $meals = $mealService
                        ->byUser($vendor_id)
                        ->hasVendor()
                        ->owner()->category()
                        ->sortByRating()
                        ->orders()
                        ->query();
            }
        }

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', "Meal"), [
            'meals' => $meals->get()
        ]);
    }

    function delete(Request $request, MealService $mealService, $meal_id){
        $mealService->find($meal_id)->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', "Meal"));
    }

    function fetchAllMeals(MealService $mealService, $vendor_id = null){
        $meals = $mealService
                        ->hasVendor()
                        ->owner()->category()
                        ->sortByRating()
                        ->orders()
                        ->query();

        return $this->returnMessageTemplate(true, '', [
            'meals' => $meals->get()
        ]);
    }

    function vendorFetchSingleMeal(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->orders()->query();

        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal->get()
        ]);
    }

    function single(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->orders()->reviews()->query();
        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal->get()
        ]);
    }

    function updateAvailabilityStatus(){

    }
}
