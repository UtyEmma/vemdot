<?php

namespace App\Models\Users;

use App\Traits\Generics;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model{
    use HasFactory, Generics;

    protected $fillable = ['unique_id', 'user_id', 'id_number', 'id_image', 'status'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => 'pending'
    ];

}
