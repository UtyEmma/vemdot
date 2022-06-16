<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'avatar' => 'required|image|max:2000000',
            'name' => 'required|string',
            'phone' => 'required|string',
            'id_number' => 'required|string',
            'id_image' => 'required|image|max:2000000',
            'logo' => 'nullable|image|max:2000000',
            'resturant_name' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'address' => 'required|string',
            'avg_time' => 'required|string'
        ];
    }
}
