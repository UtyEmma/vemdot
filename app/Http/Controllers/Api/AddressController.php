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

        Address::create($request->safe()->merge([
            'user_id' => $user->unique_id,
            'unique_id' => $unique_id
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

    function update(UpdateAddressRequest $request, Address $address){
        $user = $this->user();

        if($address->user_id !== $user->unique_id)
                    return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_owner', "Address". 'the current User'));

        $address->update($request->safe()->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', 'Address'), [
            'user' => $user->with('addresses'),
            'addresses' => $user->addresses
        ]);
    }

    function delete(Address $address){
        $user = $this->user();
        if($address->user_id !== $user->unique_id)
                        return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_owner', "Address". 'the current User'));
        $address->delete();

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', 'Address'), [
            'user' => $user->with(['addresses']),
            'addresses' => $user->addresses
        ]);
    }
}
