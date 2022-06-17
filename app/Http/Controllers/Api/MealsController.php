<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
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
        if(!$meal = Meal::find($meal_id))
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Meal'));
        $meal->update($request->safe()->all());
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', "Meal"), [
            'meal' => $meal
        ]);
    }

    function vendorMeals(Request $request, $vendor_id = null){
        if(!$user = User::where('unique_id', $vendor_id)->first() ?? $this->user())
                                        return $this->returnErrorMessage('user_not_found');

        $meals = $user->meals();
        $query = new MealService($request, $meals);
        $meals = $query->query->orderByCategory()->status()->query();

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
                        ->hasVendor('active')
                        ->owner()
                        ->category()
                        ->sortByRating()
                        ->orders()
                        ->query();

        return $this->returnMessageTemplate(true, '', [
            'meals' => $meals->get()
        ]);
    }

    function fetchSingleMeal(MealService $mealService, $meal_id){
        $meal = $mealService->query->find($meal_id)->owner()->reviews()->query();
        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal->get()
        ]);
    }
}
