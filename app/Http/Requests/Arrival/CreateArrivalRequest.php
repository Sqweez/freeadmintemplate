<?php

namespace App\Http\Requests\Arrival;

use Illuminate\Foundation\Http\FormRequest;

class CreateArrivalRequest extends FormRequest
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
            'store_id' => 'required',
            'is_completed' => 'required|boolean',
            'comment' => 'sometimes',
            'user_id' => 'required',
            'payment_cost' => 'sometimes',
            'arrived_at' => 'sometimes',
            'products' => 'required|array'
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'user_id' => auth()->id(),
            'comment' => $this->comment ?: '',
            'is_completed' => false,
        ]);
    }
}
