<?php

namespace App\Models\Meal;

use App\Models\Order;
use App\Models\Restaurant\Restaurant;
use App\Models\User;
use App\Models\Meal\Favourite;
use App\Models\Review\Review;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Meal extends Model{
    use HasFactory, SoftDeletes, HasJsonRelationships;

    protected $fillable = ['unique_id', 'user_id', 'category', 'name', 'thumbnail', 'description', 'price', 'images', 'video', 'price', 'discount', 'tax', 'rating', 'availability', 'promoted', 'avg_time', 'total_orders'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'availability' => 'yes',
        'rating' => 1,
        'discount' => 0,
        'tax' => 0,
        'promoted' => 'no',
    ];

    protected $casts = [
        'images' => 'array'
    ];

    function vendor(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }

    function categories(){
        return $this->belongsTo(MealCategory::class, 'category', 'unique_id');
    }

    function favourites(){
        return $this->hasMany(Favourite::class, 'meal_id', 'unique_id');
    }

    function orders(){
        return $this->hasMany(Order::class, 'meals->meal_id', 'unique_id');
    }

    function reviews(){
        return $this->hasMany(Review::class, 'data_id', 'unique_id');
    }


}
