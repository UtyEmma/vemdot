<?php

namespace App\Http\Requests\Api\Order;

use App\Models\User;
use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest{
    use ReturnTemplate;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
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
    public function rules() {
        return [
            'bike_id' => ['required_if:delivery_method,home', 'string', 'exists:users,unique_id'],
            'vendor_id' => 'required|string|exists:users,unique_id',
            'address_id' => [
                'required_without_all:receiver_name,receiver_phone,receiver_location,receiver_email',
                'string',
                "nullable",
                Rule::exists('addresses', 'unique_id')],
            'instructions' => 'nullable|string',
            'receiver_id' => [
                                'nullable',
                                Rule::exists('users', 'unique_id'),
                                Rule::requiredIf(fn () => $this->isNotFilled('address_id') &&  $this->isNotFilled('receiver_name'))
                            ],
            'delivery_distance' => ['numeric',
                                    'required'
                                ],
            'delivery_method' => 'required|in:home,pickup',
            'receiver_name' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'receiver_phone' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'receiver_location' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'receiver_email' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id')), "email"],
            'meals' => 'required|array',
            'payment_method' => 'required|in:wallet,card',
            'card_id' => ["nullable", Rule::exists('cards', 'unique_id')->where('user_id', $this->user()->unique_id)]
        ];
    }

    public function attributes() {
        return [
            'courier_id' => 'Courier',
            'address_id' => "Address",
            'receiver_id' => "Receiver",
            'delivery_method' => "Delivery Method",
            "delivery_distance" => "Delivery Distance",
            "reciever_name" => "Receiver Name",
            'bike_id' => "Rider",
            'vendor_id' => "Vendor",
        ];
    }
}
