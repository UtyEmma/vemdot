<?php

namespace App\Models\Meal;

use App\Models\Restaurant\Restaurant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'restaurant_id', 'category_id', 'name', 'thumbnail', 'description', 'price', 'images', 'video', 'price', 'discount', 'tax', 'rating', 'availability'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'availability' => true
    ];

    function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'unique_id');
    }
}
