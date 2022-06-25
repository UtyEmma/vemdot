<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Bank\BankList;

class BankDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'bank_id', 'account_name', 'account_no'];

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    function bank(){
        return $this->belongsTo(BankList::class, 'bank_id', 'code');
    }
}
