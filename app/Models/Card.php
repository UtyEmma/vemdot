<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model{
    use HasFactory;

    protected $fillable = ['unique_id', 'user_id', 'auth_code', 'signature', 'data'];

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'data' => 'array'
    ];


}
