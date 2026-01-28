<?php

namespace App\Http\Requests;

use App\Rules\FacebookPageUrl;
use App\Rules\GoogleMapsUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class BusinessDetailRequest extends FormRequest
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
            'established' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y')],
            'number_of_employee' => 'nullable',
            'direction' => ['nullable', new GoogleMapsUrl],
            'tin' => 'nullable|digits:12',
            'website' => 'nullable|url',
            'facebook' => ['nullable', new FacebookPageUrl],
            'video_url' => 'nullable|url',
            'timing' => 'nullable|array'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!',$validator->errors()));
    }
}
