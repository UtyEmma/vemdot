<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['unique_id', 'user_id', 'admin_id', 'message', 'file', 'send_status', 'status'];

    protected $attributes = [
        'status' => 'unread',
        'send_status' => 'user',
        'admin_id' => null
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id', 'unique_id');
    } 

    function admin(){
        return $this->belongsTo(User::class, 'admin_id', 'unique_id');
    } 
}
