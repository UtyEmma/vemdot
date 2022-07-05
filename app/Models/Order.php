<?php

namespace App\Models;

use App\Traits\Options;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory, Options;

    protected $fillable = [
        'unique_id', 'user_id', 'courier_id', 'bike_id', 'transaction_id', 'address_id', 'instructions', 'amount', 'receiver_id', 'delivery_fee', 'delivery_method', 'receiver_name', 'receiver_phone', 'receiver_location', 'status', 'delivery_distance', 'meals', 'vendor_id'
    ];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'pending'
    ];

    protected $casts = [
        'meals' => 'array'
    ];

    public function orderStatus(){
        return $this->hasMany(OrderStatus::class, 'order_id', 'unique_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }

    public function vendor(){
        return $this->belongsTo(User::class, 'vendor_id', 'unique_id');
    }

    public function courier(){
        return $this->belongsTo(User::class, 'courier_id', 'unique_id');
    }

    public function bike(){
        return $this->belongsTo(User::class, 'bike_id', 'unique_id' );
    }

}
