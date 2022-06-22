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
use Twilio\Rest\Client;
use App\Traits\Generics;

class User extends Authenticatable{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes, Generics;

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
        'kyc_status' => 'pending',
        'availability' => 'yes'
    ];

    public function generateCode($auth){
        $code = rand(1000, 9999);

       // $auth = auth()->user();

        $faildCode = UserCode::where([
            ['user_id', $auth->unique_id],
            ['status', 'un-used']
        ])->first();

        if($faildCode != null){
            $faildCode->status = 'failed';
            $faildCode->save();
        }

        UserCode::create([
            'unique_id' => $this->createUniqueId('user_codes'),
            'user_id' => $auth->unique_id,
            'code' => $code
        ]);

        $message = "Your 2FA login code is ". $code;

        try {
            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_TOKEN");
            $twilio_number = env("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($auth->phone, [
                'from' => $twilio_number,
                'body' => $message
            ]);

            return ['status' => true, 'code' => $code];
        } catch (Exception $e) {
            info("Error: ". $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }


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
