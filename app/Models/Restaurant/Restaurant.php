<?php

namespace App\Models\Restaurant;

use App\Models\Meal\Meal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'name', 'city', 'state', 'address', 'avg_time', 'logo', 'status', 'availability'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'status' => true,
        'availability' => true
    ];

    public function meals(){
        return $this->hasMany(Meal::class, 'restaurant_id', 'unique_id');
    }
}
