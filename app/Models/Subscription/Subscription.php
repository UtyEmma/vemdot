<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'plan_id', 'user_id', 'meal_id', 'start_date', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    function owner(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }

    protected $casts = [
        'meal_id' => 'array'
    ];
}
