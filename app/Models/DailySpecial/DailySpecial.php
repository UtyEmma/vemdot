<?php

namespace App\Models\DailySpecial;

use App\Models\User;
use App\Models\Meal\Meal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailySpecial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'meal_id', 'no_views', 'status', 'caption'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    function meals(){
        return $this->belongsTo(Meal::class, 'meal_id', 'unique_id');
    }

    function owner(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }
}
