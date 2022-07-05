<?php

namespace App\Models\Withdrawal;

use App\Models\User;
use App\Models\Bank\BankDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletWithdrawal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id', 'user_id', 'bank_id', 'type', 'amount', 'payment_method', 'recipient_code', 'integration', 'reference', 'transfer_code', 'description', 'status'];

    protected $keyType = 'string';
    protected $primaryKey = 'unique_id';
    public $incrementing = false;

    protected $attributes = [
        'amount' => 0,
        'status' => 'pending',
    ];

    function owner(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    } 
    
    function bankDetails(){
        return $this->belongsTo(BankDetail::class, 'bank_id', 'unique_id');
    }
}
