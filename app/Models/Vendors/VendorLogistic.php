<?php

namespace App\Models\Vendors;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorLogistic extends Model{
    use HasFactory;

    protected $fillable = ['vendor_id', 'unique_id', 'company_id'];

    protected $primaryKey = 'unique_id';
    protected $keyType = 'string';
    public $incrementing = false;

    function company(){
        return $this->hasOne(User::class, 'unique_id', 'company_id');
    }
}
