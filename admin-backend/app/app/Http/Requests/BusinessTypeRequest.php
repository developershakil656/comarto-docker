<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class BusinessTypeRequest extends FormRequest
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
            'type' => [
                Rule::unique('business_types')->ignore($this->route('type')),
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
