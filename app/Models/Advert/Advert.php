<?php

namespace App\Models\Advert;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'email', 'caption', 'banner', 'description', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'active',
        'email' => null,
        'caption' => null,
    ];
}
