<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Meal\Meal;
use App\Models\Role\AccountRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'unique_id', 'name', 'email', 'referral_id', 'referred_id', 'role',
        'status',
        'country',
        'phone',
        'gender',
        'avatar',
        'main_balance',
        'ref_balance',
        'password',
        'business_name',
        'city',
        'state',
        'state',
        'address',
        'avg_time',
        'logo',
        'id_number',
        'id_image',
        'kyc_status',
        'availability'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $attributes = [
        'kyc_status' => 'pending'
    ];


    public function getAllUsers($condition, $id = 'id', $desc = "desc"){
        return User::where($condition)->orderBy($id, $desc)->get();
    }

    public function paginateUsers($num, $condition, $id = 'id', $desc = "desc"){
        return User::where($condition)->orderBy($id, $desc)->paginate($num);
    }

    public function getUser($condition){
        return User::where($condition)->first();
    }

    public function get_roles(){
        $roles = [];
        foreach ($this->getRoleNames() as $key => $role) {
            $roles[$key] = $role;
        }

        return $roles;
    }

    public function meals (){
        return $this->hasMany(Meal::class, 'user_id', 'unique_id');
    }

    public function addresses(){
        return $this->hasMany(Address::class, 'user_id', 'unique_id');
    }

    public function userRole(){
        return $this->belongsTo(AccountRole::class, 'role', 'unique_id');
    }
}
