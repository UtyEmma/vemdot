<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'order_id', 'status'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

}
