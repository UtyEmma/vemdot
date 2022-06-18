<?php

namespace App\Models\Meal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealCategory extends Model {
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getAllMealCategorys($condition, $id = 'id', $desc = "desc"){
        return MealCategory::where($condition)->orderBy($id, $desc)->get();
    }

    public function paginateMealCategorys($num, $condition, $id = 'id', $desc = "desc"){
        return MealCategory::where($condition)->orderBy($id, $desc)->paginate($num);
    }

    public function getMealCategory($condition){
        return MealCategory::where($condition)->first();
    }
}
