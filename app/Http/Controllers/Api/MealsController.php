<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Request;

class MealsController extends Controller{

    function create(CreateMealRequest $request){
        $user = $this->user();
        $unique_id = $this->createUniqueId('meals');

        Meal::create($request->safe()->merge([
            'unique_id' => $unique_id
        ])->all());

        return $this->returnMessageTemplate(true, "Your Meal was created Successfully!");
    }

    function update(CreateMealRequest $request, $meal_id){
        if(!$meal = Meal::find($meal_id))
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Meal'));

        $meal->update($request->safe()->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', "Meal"));
    }

    function vendorMeals(Request $request){
        $user = $this->user();
        $meals = $user->meals();

        $meals->when($request->has('category'), function($query, $category){
            $query->whereRelation('category', 'category_id', $category);
        });

        $meals->when($request->has('status'), function($query, $status){
            $availability = $status === 'available';
            $query->where('availability', $availability);
        });

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', "Meals"), [
            'meals' => $meals->get()
        ]);
    }

    function fetchAllMeals(Request $request){
        $meals = Meal::query();

        $meals->whereRelation('owner', 'status', false);

        $meals->when($request->has('category'), function($query, $category){
            $query->whereRelation('category', $category);
        });

        $meals->when($request->has('status'), function($query, $status){
            $availability = $status === 'available';
            $query->where('availability', $availability);
        });
    }


}
