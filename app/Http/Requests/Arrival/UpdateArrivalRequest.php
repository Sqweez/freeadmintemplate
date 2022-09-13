<?php

namespace App\Http\Requests\Arrival;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArrivalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'comment' => 'sometimes',
            'arrived_at' => 'sometimes',
            'payment_cost' => 'sometimes',
            'products' => 'required|array'
        ];
    }
}
