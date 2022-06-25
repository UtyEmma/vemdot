<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'name', 'slug', 'code', 'longcode', 'country', 'currency', 'status', 'logo'];

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

}
