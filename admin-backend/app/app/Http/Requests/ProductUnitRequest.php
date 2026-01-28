<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class ProductUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'short_form' => [
                Rule::unique('product_units')->ignore($this->route('unit')),
                'required',
                'string'
            ],
            'plural_form' => [
                Rule::unique('product_units')->ignore($this->route('unit')),
                'required',
                'string'
            ],
            'full_form' => [
                Rule::unique('product_units')->ignore($this->route('unit')),
                'required',
                'string'
            ],
            'status' => 'required|in:active,blocked',
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(error_response('validation error!',$validator->errors()));
    }
}
