<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getAllAccountRoles($condition, $id = 'id', $desc = "desc"){
        return AccountRole::where($condition)->orderBy($id, $desc)->get();
    }

    public function paginateAccountRoles($num, $condition, $id = 'id', $desc = "desc"){
        return AccountRole::where($condition)->orderBy($id, $desc)->paginate($num);
    }

    public function getAccountRole($condition){
        return AccountRole::where($condition)->first();
    }
}
