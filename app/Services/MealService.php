<?php

namespace App\Services;

use App\Models\Meal\Meal;
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

    function byUser($user_id){
        $this->query = $this->query->where('user_id', $user_id);
        return $this;
    }

    function orders(string|bool $count = false){
        if(!$count) $this->query = $this->query->with('orders');
        if($count) $this->query = $this->query->withCount('orders');
        if($count === 'withCount') $this->query = $this->query->with('orders')->withCount('orders');
        return $this;
    }

    function orderByCategory($category = 'category'){
        $this->query = $this->query->when(request()->has($category), function($query, $category){
            $query->whereRelation('category', $category);
        });
        return $this;
    }

    function status($keyword = 'status'){
        $this->query = $this->query->when(request()->has($keyword), function($query, $status){
            $availability = $status === 'available' ? 'yes' : 'no';
            $query->where('availability', $availability);
        });
        return $this;
    }

    function search($keyword = 'search'){
        $this->query = $this->query->when(request()->has($keyword), function($query, $keyword){
            $query->where('name', '%like%', $keyword);
        });
        return $this;
    }

    function sortByPopularity($keyword = 'sortBy'){
        $this->query= $this->query->when(request()->has($keyword), function($query, $sort){
            if($sort === 'popularity'){
                $query->orderBy(User::where('unique_id', $query->user_id)->count());
            }
        });

        return $this;
    }

    function sortByRating($keyword = 'sortBy'){
        $this->query = $this->query->when(request()->input($keyword), function($query, $sort){
            if($sort === 'rating'){
                $query->orderBy('rating');
            }
        });
        return $this;
    }

    function delete(){
        $this->query = $this->query->delete();
    }

    function reviews(){
        // return $this->query = $query;
        return $this;
    }

    function getOrder($condition, $paginate){
        return Meal::where($condition)->orderBy('id', 'desc')->paginate($paginate);
    }
}

// $meals = Meal::query();

// $meals->whereRelation('owner', 'status', $this->active);

// $meals->withCount('orders');

// $meals->when($request->has('category'), function($query, $category){
//     $query->whereRelation('category', $category);
// });

// $meals->when($request->has('status'), function($query, $status){
//     $availability = $status === 'available';
//     $query->where('availability', $availability);
// });

// $meals->when($request->has('keyword'), function($query, $keyword){
//     $query->where('name', '%like%', $keyword);
// });

// $meals->when($request->has('sortBy'), function($query, $sort){
//     if($sort === 'rating'){
//         $query->orderBy($sort);
//     }

//     if($sort === 'popularity'){
//         $query->orderBy(User::where('unique_id', $query->user_id)->count());
//     }
// });

// $meals->when($request->has('delivery'), function($query, $delivery) use($request){
//     if($delivery === 'lessThan'){
//         $query->where('avg_time', '<==', $request->time);
//     }

//     if($delivery === 'equalTo'){
//         $query->where('avg_time', '===', $request->time);
//     }

//     if($delivery === 'greaterThan'){
//         $query->where('avg_time', '>==', $request->time);
//     }
// });
