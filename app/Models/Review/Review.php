<?php

namespace App\Models\Review;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Review extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'type', 'data_id', 'rate', 'comment', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'pending',
        'rate' => 0,
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    }
}
