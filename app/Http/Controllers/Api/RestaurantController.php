<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Restaurant\CreateRestaurantRequest;
use App\Models\Restaurant\Restaurant;
use App\Traits\FileUpload;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Http\Request;

class RestaurantController extends Controller{
    use ReturnTemplate, FileUpload, Generics;

    function create(CreateRestaurantRequest $request){
        $user = $this->user();
        $logo = $this->uploadImageHandler($request, 'logo', 'restaurant');

        $restaurant = Restaurant::create($request->safe()
                                    ->merge([
                                        'user_id' => $user->unique_id,
                                        'unique_id' => $this->createUniqueId('restaurants', 'unique_id'),
                                        'name' => $request->name,
                                        'logo' => $logo
                                    ])->only(['name', 'unique_id', 'user_id', 'logo', 'city', 'state', 'address', 'avg_time']));

        return $this->returnMessageTemplate(true, "Resturant Created Sucessfully", [
                                                                        'current_restaurant' => $restaurant,
                                                                        'all_restaurants' => $user->restaurants
                                                                    ]);
    }

    public function show($id){
        $restaurant = Restaurant::findOrFail($id);

        return $this->returnMessageTemplate(true, "", $restaurant);
    }

    public function update(CreateRestaurantRequest $request, $id){
        $restaurant = Restaurant::findOrFail($id);

        $logo = $this->uploadImageHandler($request, 'logo', 'restaurant', $restaurant->logo);

        $restaurant->update($request->safe()->merge(['logo' => $logo])->all());

        return $this->returnMessageTemplate(true, "Resturant Created Sucessfully", [
            'restaurant' => $restaurant
        ]);
    }

    public function destroy($id){
        $user =  $this->user();

        $restaurant = Restaurant::findOrFail($id);
        $meals = $restaurant->meals;
        $meals->delete();

        $restaurant->delete();
        $restaurants = $user->restaurants;

        return $this->returnMessageTemplate(true, "Resturant Created Sucessfully", [
            'restaurants' => $restaurants
        ]);
    }
}
