<?php

namespace App\Http\Requests\Api\Users;

use App\Models\User;
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
        $user = User::find($this->user()->unique_id);
        return [
            'avatar' => 'nullable|string|url',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->unique_id, 'unique_id')],
            'id_number' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'id_image' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'url'],
            'logo' => 'nullable|string|url',
            'business_name' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'city' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'state' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'address' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'avg_time' => [Rule::requiredIf($user->isLogistic() || $user->isVendor()), 'string'],
            'password' => 'nullable|confirmed',
            'old_password' => 'required_with:password'
        ];
    }
}
