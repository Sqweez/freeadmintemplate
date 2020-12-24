<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class ProductCreateRequest extends FormRequest
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
            'product_name' => 'required',
            'product_description' => 'required',
            'product_price' => 'required|numeric|min:1',
            'product_barcode' => 'required',
            'category' => 'required|numeric',
            'subcategory' => 'required|numeric',
            'manufacturer' => 'required|numeric',
        ];
    }

    public function messages() {
        return [
            'product_name.required' => 'Не заполнено поле название товара',
            'product_description.required' => 'Не заполнено поле описание товара',
            'product_price.required' => 'Не заполнено поле цена товара',
            'product_price.numeric' => 'Поле цена товара должно быть числом',
            'product_barcode.required' => 'Не заполнено поле штрихкод',
            'category.required' => 'Не указана категория товара',
            'subcategory.required' => 'Не указана подкатегория товара',
            'manufacturer.required' => 'Не указан производитель товара',
            'is_hit.required' => 'Не указано значение "хит продаж"',
            'is_site_visible.required' => 'Не указано значение "виден на сайте"',
        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['message' => $validator->errors()->first()], 422));
    }
}
