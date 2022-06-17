<?php

namespace App\Http\Requests\Api\Meals;

use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateMealRequest extends FormRequest{
    use ReturnTemplate;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
    public function rules()
    {
        return [
            'name' => ['required','string'],
            'thumbnail' => 'required|url',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'images*' => 'required|url',
            'video' => 'required|url',
            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'category_id' => 'required|string'
        ];
    }
}
