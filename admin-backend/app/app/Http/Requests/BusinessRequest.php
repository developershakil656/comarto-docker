<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Validation\Rule;

class BusinessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        // Check if we are in an "update" scenario
        // Assuming your route parameter is named 'business' (e.g., businesses/{business})
        $businessId = $this->route('id');

        return [
            'name' => 'required',
            // Logic: Required on POST (create), sometimes/optional on PUT/PATCH (update)
            'slug' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'alpha_dash',
                'min:3',
                'max:50',
                // Unique check: Ignore current ID during update to prevent validation failure
                Rule::unique('businesses', 'slug')->ignore($businessId),
            ],
            'business_name' => 'required',
            'number' => 'required|digits:11',
            'alternate_number' => 'nullable|digits:11',
            'email' => 'email|nullable',
            'address' => 'required',
            'location_id' => 'required|integer',
            'post_code' => 'required|digits:4',
            'business_type' => 'required|array',
            'business_type.*' => 'required|string',
            'status' => 'in:active,inactive',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}