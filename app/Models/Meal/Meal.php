<?php

namespace App\Models\Meal;

use App\Models\Order;
use App\Models\Restaurant\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'category', 'name', 'thumbnail', 'description', 'price', 'images', 'video', 'price', 'discount', 'tax', 'rating', 'availability', 'promoted'];

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

    function vendor(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }

    function categories(){
        return $this->belongsTo(MealCategory::class, 'category', 'unique_id');
    }

    function orders(){
        return $this->belongsTo(Order::class, 'order_id', 'unique_id');
    }


}
