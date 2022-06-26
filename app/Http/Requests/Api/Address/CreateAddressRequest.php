<?php

namespace App\Http\Requests\Api\Address;

use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateAddressRequest extends FormRequest{
    use ReturnTemplate;

    public function authorize(){
        return true;
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException($this->returnMessageTemplate(false, $validator->errors()));
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'name' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'location' => 'required|string',
            'phone' => 'nullable|string'
        ];
    }
}
