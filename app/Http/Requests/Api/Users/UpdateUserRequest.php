<?php

namespace App\Http\Requests\Api\Users;

use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest{
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
    public function rules(){
        return [
            'avatar' => 'nullable|image|max:2000000',
            'name' => 'required|string',
            'phone' => 'required|string',
            'id_number' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
            'id_image' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'image', 'max:2000000'],
            'logo' => 'nullable|image|max:2000000',
            'business_name' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
            'city' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
            'state' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
            'address' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
            'avg_time' => [Rule::requiredIf(in_array($this->role, ['vendor', 'logistics'])), 'string'],
        ];
    }
}
