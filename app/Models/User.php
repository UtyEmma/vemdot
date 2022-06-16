<?php

namespace App\Models;

use App\Models\Restaurant\Restaurant;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unique_id',
        'name',
        'email',
        'referral_id',
        'referred_id',
        'role',
        'status',
        'country',
        'phone',
        'gender',
        'avatar',
        'main_balance',
        'ref_balance',
        'password',
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

    public function restaurants (){
        return $this->hasMany(Restaurant::class, 'user_id', 'unique_id');
    }
}
