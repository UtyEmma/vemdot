<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\User;
use App\Services\MealService;
use App\Services\NotificationService;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    function vendorMeals(Request $request, $vendor_id = null){
        if(!$user = User::where('unique_id', $vendor_id)->first() ?? $this->user()) return $this->returnErrorMessage('user_not_found');

        $meals = $user->meals();
        $query = new MealService($request, $meals);
        $meals = $query->category()->status()->query();

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', "Meals"), [
            'meals' => $meals->get()
        ]);
    }

    function deleteMeal(Request $request, MealService $mealService, $meal_id){
        $mealService->find($meal_id)->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', "Meal"));
    }

    function fetchAllMeals(MealService $mealService){
        $meals = $mealService
                        ->hasVendor($this->pending)
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

    function fetchSingleMeal(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->query();
        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal->get()
        ]);
    }

    function updateAvailabilityStatus(){

    }
}
