<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManufacturerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'manufacturer_name' => 'required',
            'manufacturer_img' => 'sometimes|file',
            'manufacturer_description' => 'sometimes',
            'show_on_main' => 'required'
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'show_on_main' => isset($this->show_on_main) && $this->show_on_main === 'true',
        ]);
    }
}
