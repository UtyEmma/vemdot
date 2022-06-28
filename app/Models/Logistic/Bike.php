<?php

namespace App\Models\Logistic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User;

class Bike extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $fillable = ['unique_id', 'user_id', 'name', 'email', 'avatar', 'bike_no', 'bike_image', 'password', 'availability', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'availability' => 'yes',
        'status' => 'pending',
        'avatar' => 'default.png',
        'bike_image' => 'default.png',
    ];

    function logistic(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }
}
