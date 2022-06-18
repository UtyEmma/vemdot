<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';
  
    protected $fillable = [
        'unique_id',
        'user_id',
        'code',
    ];

    public function users(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
