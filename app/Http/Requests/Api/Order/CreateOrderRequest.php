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
            'courier_id' => 'required|string|exists:users,unique_id',
            'vendor_id' => 'required|string|exists:users,unique_id',
            'address_id' => ['string', Rule::exists('addresses', 'unique_id')->where('user_id', $this->user()->unique_id)],
            'instructions' => 'nullable|string',
            'receiver_id' => [
                                'nullable',
                                Rule::exists('users', 'unique_id'),
                                Rule::requiredIf(fn () => $this->isNotFilled('address_id') &&  $this->isNotFilled('receiver_name'))
                            ],
            'delivery_fee' => ['numeric', Rule::requiredIf(fn () => $this->isNotFilled('delivery_distance'))],
            'delivery_distance' => ['numeric',
                                    'required',
                                    // Rule::requiredIf(fn () => $this->isNotFilled('delivery_fee'))
                                ],
            'delivery_method' => 'required|in:home,pickup',
            'receiver_name' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'receiver_phone' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'receiver_location' => [Rule::requiredIf(fn () => $this->isNotFilled('address_id'))],
            'meals' => 'required',
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
            "delivery_fee" => "Delivery Fee",
            "delivery_distance" => "Delivery Distance",
            "reciever_name" => "Receiver Name"
        ];
    }
}
