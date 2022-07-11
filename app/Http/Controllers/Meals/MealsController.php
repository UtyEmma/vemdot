<?php

namespace App\Http\Controllers\Meals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\Order;
use App\Services\MealService;
use Illuminate\Support\Facades\Request;

class MealsController extends Controller{

    function __construct(MealService $mealService){
        $this->mealService = $mealService;
    }

    function create(CreateMealRequest $request){
        $user = $this->user();

        if(!MealCategory::find($request->category)) return $this->returnMessageTemplate(false, "The Selected Meal Category Does not Exist");

        $unique_id = $this->createUniqueId('meals');

        $meal = Meal::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'user_id' => $user->unique_id,
            'images' => $request->images
        ])->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Meal'), [
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
                        ->filterByCategory()
                        ->status()
                        ->query()->paginate();
        }else if($vendor_id){
            $meals = $mealService
                        ->byUser($vendor_id)
                        ->hasVendor()
                        ->filterByCategory()
                        ->owner()->category()
                        ->sortByRating()
                        ->query()->paginate();
        }else{
            return $this->returnMessageTemplate(false, 'Invalid Request. You are not logged in as a Vendor');
        }

        foreach ($meals as $meal) {
            $orders = Order::whereJsonContains('meals', ['meal_id' => $meal->unique_id])->get();
            $meal->orders = $orders;
            $meal->order_count = $orders->count();
        }

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', "Meal"), [
            'meals' => $meals
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
                        ->search()
                        ->filterByCategory()
                        ->status()
                        ->query()->paginate();

        foreach ($meals as $meal) {
            $orders = Order::whereJsonContains('meals', ['meal_id' => $meal->unique_id])->get();
            $meal->orders = $orders;
            $meal->order_count = $orders->count();
        }

        return $this->returnMessageTemplate(true, '', [
            'meals' => $meals
        ]);
    }

    function vendorFetchSingleMeal(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->query();

        $orders = Order::whereJsonContains('meals', ['meal_id' => $meal_id])->get();
        $meal = $meal->first();
        $meal->orders = $orders;

        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal
        ]);
    }

    function single(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->query();
        if(!$meal->exists()) return $this->returnMessageTemplate(false, "Meal Not found");
        $orders = Order::whereJsonContains('meals', ['meal_id' => $meal_id])->count();
        $meal = $meal->first();
        $meal->order_count = $orders;
        return $this->returnMessageTemplate(true, '', [
            'meal' => $meal,
        ]);
    }

    function fetchMealsByAds(){
        $meals = Meal::where('promoted', $this->yes)->paginate($this->paginate);
        foreach($meals as $meal){
            $meal->categories;
            $meal->vendor;
        }
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Meal'), $meals);
    }
}
