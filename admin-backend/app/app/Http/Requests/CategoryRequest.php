<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            'name' => [
                Rule::unique('categories')->ignore($this->route('id')),
                'required',
                'max:50'
            ],
            'icon' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:active,blocked',
            'parent_id' => 'integer|nullable'
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(error_response('validation error!',$validator->errors()));
    }
}
