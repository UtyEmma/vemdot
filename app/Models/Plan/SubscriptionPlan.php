<?php

namespace App\Models\Plan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getAllSubscriptionPlans($condition, $id = 'id', $desc = "desc"){
        return SubscriptionPlan::where($condition)->orderBy($id, $desc)->get();
    }

    public function paginateSubscriptionPlans($num, $condition, $id = 'id', $desc = "desc"){
        return SubscriptionPlan::where($condition)->orderBy($id, $desc)->paginate($num);
    }

    public function getSubscriptionPlan($condition){
        return SubscriptionPlan::where($condition)->first();
    }
}
