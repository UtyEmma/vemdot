<?php

namespace App\Models\Meal;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model {
    use HasFactory;

    protected $fillable = ['unique_id', 'meal_id', 'user_id'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function users(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }
}
