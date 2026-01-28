<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class FollowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $decoded = Hashids::decode($this->business_id);

        if (empty($decoded)) {
            // Decoding failed — set business_id as null
            $this->merge(['business_id' => null]);
        } else {
            // Valid — set numeric business_id
            $this->merge(['business_id' => $decoded[0]]);
        }
    }

    public function rules(): array
    {
        return [
            'business_id' => [
                'required',
                Rule::unique('followings')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}
