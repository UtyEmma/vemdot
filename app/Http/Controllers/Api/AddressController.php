<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Address\CreateAddressRequest;
use App\Http\Requests\Api\Address\UpdateAddressRequest;
use App\Models\Address\Address;
use App\Models\User;
use App\Traits\Generics;
use Illuminate\Http\Request;

class AddressController extends Controller{
    use Generics;

    function create(CreateAddressRequest $request){
        $user = $this->user();
        $unique_id = $this->createUniqueId('addresses');

        $default = $user->addresses()->count() > 0 ? $this->no : $this->yes;

        Address::create($request->safe()->merge([
            'user_id' => $user->unique_id,
            'unique_id' => $unique_id,
            'default' => $default
        ])->all());

        return $this->returnMessageTemplate(true, "Address Created", [
            'user' => $user->with('addresses'),
            'addresses' => $user->addresses
        ]);
    }

    function list(){
        $user = $this->user();
        return $this->returnMessageTemplate(true, '', [
            'user' => $user->with('addresses'),
            'addresses' => $user->addresses
        ]);
    }

    function update(UpdateAddressRequest $request, $id){
        $address = Address::find($id);
        if(!$address) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Address"));

        $user = $this->user();

        if($address->user_id !== $user->unique_id)
        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_owner', "Address", 'the current User'));

        $address->update($request->safe()->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Address'), [
            'user' => $user->with('addresses'),
            'addresses' => $user->addresses
        ]);
    }

    function single($id){
        $address = Address::find($id);
        if(!$address) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Address"));
        return $this->returnMessageTemplate(true, "", [
            'address' => $address
        ]);
    }

    function delete($id){
        $address = Address::find($id);

        if(!$address) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'Address'));
        $user = $this->user();

        if($address->user_id !== $user->unique_id)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_owner', "Address", 'the current User'));

        $address->delete();

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', 'Address'), [
            'user' => $user->with(['addresses']),
            'addresses' => $user->addresses
        ]);
    }

    function setDefault($id){
        $address = Address::find($id);
        if(!$address) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', "Address"));

        $user = $this->user();

        if($address->user_id !== $user->unique_id)
                        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_owner', "Address", 'the current user'));

        $user->addresses()->where('default', $this->yes)->update(['default' => $this->no]);

        $address->default = $this->yes;
        $address->save();

        return $this->returnMessageTemplate(true, "$address->name address has been set to default", [
            'addresses' => $user->addresses,
            'address' => $address
        ]);
    }
}
