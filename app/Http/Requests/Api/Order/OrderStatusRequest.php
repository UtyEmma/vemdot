<?php

namespace App\Http\Requests\Api\Order;

use App\Traits\Options;
use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderStatusRequest extends FormRequest {
    use ReturnTemplate, Options;

    public function authorize(){
        return true;
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException($this->returnMessageTemplate(false, $validator->errors()));
    }


    public function rules(){
        return [
            'status' => "in:".implode(',', $this->orderProgression)
        ];
    }
}
