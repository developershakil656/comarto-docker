<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class FavouriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $decoded = Hashids::decode($this->product_id);

        if (empty($decoded)) {
            // Decoding failed — set product_id as null
            $this->merge(['product_id' => null]);
        } else {
            // Valid — set numeric product_id
            $this->merge(['product_id' => $decoded[0]]);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                Rule::unique('favourites')->where(function ($query) {
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
