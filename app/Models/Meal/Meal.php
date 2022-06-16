<?php

namespace App\Models\Meal;

use App\Models\Restaurant\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'category_id', 'name', 'thumbnail', 'description', 'price', 'images', 'video', 'price', 'discount', 'tax', 'rating', 'availability'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'availability' => true
    ];

    function owner(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }


}
