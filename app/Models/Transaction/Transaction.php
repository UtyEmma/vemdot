<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'type', 'amount', 'payment_method', 'channel', 'reference', 'orderID', 'description', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;
}
