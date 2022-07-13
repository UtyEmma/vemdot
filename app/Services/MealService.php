<?php

namespace App\Services;

use App\Models\Meal\Meal;
use App\Models\Order;
use App\Models\User;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;

class MealService {
    use ReturnTemplate, Generics;

    private $query;

    function __construct(Meal $meal = null){
        $this->query = $meal ?? Meal::query();
        return $this->query;
    }


    function query(){
        return $this->query;
    }

    function find($id, $column = 'unique_id'){
        $query = $this->query->where($column, $id);
        $this->query = $query;
        return $this;
    }

    function hasVendor($status = 'confirmed'){
        $this->query = $this->query->whereRelation('vendor', 'status', $status);
        return $this;
    }

    function owner(){
        $this->query = $this->query->with('vendor');
        return $this;
    }

    function category(){
        $this->query = $this->query->with('categories');
        return $this;
    }

    function byUser($user_id = null){
        $this->query = $this->query->when($user_id, function($query, $user_id){
            $query->where('user_id', $user_id);
        });
        return $this;
    }

    function orders(string|bool $count = false){
        if(!$count) $this->query = $this->query->with('orders');
        if($count) $this->query = $this->query->withCount('orders');
        if($count === 'withCount') $this->query = $this->query->with('orders')->withCount('orders');
        return $this;
    }

    function filterByCategory($category = "category"){
        $this->query = $this->query->when(request()->input($category), function($query, $category){
            $query->where('category', $category);
        });
        return $this;
    }

    function status($availability = "availability"){
        $this->query = $this->query->when(request()->input($availability), function($query, $status){
            $query->where('availability', $status);
        });
        return $this;
    }

    function search($keyword = "keyword"){
        $this->query = $this->query->when(request()->input($keyword), function($query, $keyword){
            $query->where('name', 'LIKE', "%$keyword%");
        });
        return $this;
    }

    function orderBy($keyword = 'order'){
        $this->query = $this->query->when(request()->input($keyword), function($query, $order){
            if($order === 'rating'){
                $query->orderBy('rating', 'desc');
            }

            if($order === 'popular'){
                $query->orderBy('total_orders', 'desc');
            }
        });
        return $this;
    }

    function delete(){
        $this->query = $this->query->delete();
    }

    function reviews(){
        $this->query->with('reviews')->withCount('reviews');
        return $this;
    }

    function groupCategory(){
        $this->query = $this->query->groupBy('category_id');
        return $this;
    }

    function mealCategories () {
        // $this->query = $this->query->groupBy('category');
        return $this;
    }
}
