<?php

namespace App\Http\Controllers\Meals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MealService;
use App\Models\Meal\MealCategory;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class AdminMealController extends Controller
{
    //

    protected function getAvaliableMeals(MealService $mealService, $startDate = null, $endDate = null){
        $condition = [
            ['availability', $this->yes]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        if($startDate !== null){
            $condition[] = ['category', $startDate];
        }

        $payload = [
            'categories' => MealCategory::all(),
            'meals' => $mealService->getOrder($condition, $this->paginate),
        ];
        return view('pages.meal.avaliable-meals', $payload);
    }
    protected function getMealByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('meals/interface/'.$startDate.'/'.$endDate);
    }
    protected function getByCategory(Request $request){
        $type = $request->user_type;
        return redirect()->to('meals/interface/'.$type);
    } 
    //get all meals
    protected function getPromotedMeals(MealService $mealService, $startDate = null, $endDate = null){
        $condition = [
            ['promoted', $this->yes]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
        }

        if($startDate !== null){
            $condition[] = ['category', $startDate];
        }

        $payload = [
            'categories' => MealCategory::all(),
            'meals' => $mealService->getOrder($condition, $this->paginate),
        ];
        return view('pages.meal.meal-histroy', $payload);
    }
    protected function getMealsByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('meals/history/interface/'.$startDate.'/'.$endDate);
    }
    protected function getMealsByCategory(Request $request){
        $type = $request->user_type;
        return redirect()->to('meals/history/interface/'.$type);
    }

    protected function deleteMeal(MealService $mealService, Request $request){
        $mealService->find($request->unique_id)->delete();
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Meal'));
        return redirect()->back();
    }
}
