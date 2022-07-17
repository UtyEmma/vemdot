<?php

namespace App\Http\Controllers\Meals;

use App\Http\Controllers\Controller;
use App\Models\Meal\Meal;
use App\Models\Meal\Favourite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FavouriteController extends Controller{


    function toggle($meal_id){
        if(!$meal = Meal::find($meal_id)) return $this->returnMessageTemplate(false, "Meal not found");
        $user = auth()->user();

        $favourite = Favourite::where([
            'user_id' => $user->unique_id,
            'meal_id' => $meal_id
        ]);

        if($favourite->exists()){
            $favourite->delete();
            $message = "Removed from";
        }else{
            $unique_id = $this->createUniqueId('favourites');

            Favourite::create([
                'unique_id' => $unique_id,
                'user_id' => $user->unique_id,
                'meal_id' => $meal_id
            ]);

            $message = "Added to";
        }

        return $this->returnMessageTemplate(true, "Meal $message Favourites", [
            'meal' => Meal::where('unique_id', $meal_id)->whereRelation('favourites', 'user_id', $user->unique_id)->first()
        ]);
    }

    function list(){
        $user = auth()->user();
        $meals = Meal::whereRelation('favourites', 'user_id', $user->unique_id)
                        ->with(['vendor', 'categories', 'orders'])
                        ->withExists([
                            'favourites as is_favourite' => function($query) use($user){
                                return $query->where('user_id', $user->unique_id);
                            }])
                        ->get();

        return $this->returnMessageTemplate(true, '', $meals);
    }

}
