<?php

namespace App\Http\Requests\Promocode;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePromocodeRequest extends FormRequest
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
            'promocode' => 'required',
            'client_id' => 'present|nullable',
            'discount' => 'present|nullable',
            'is_active' => 'boolean',
            'active_until' => 'present|nullable',
            'promocode_type_id' => 'required',
            'free_product_id' => 'present|nullable',
            'required_products' => 'present|nullable',
            'brand_id' => 'present|nullable'
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'is_active' => true,
            'discount' => $this->discount ?: 0,
        ]);
    }
}
