<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'plan_id', 'user_id', 'meal_id', 'start_date', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;
}
