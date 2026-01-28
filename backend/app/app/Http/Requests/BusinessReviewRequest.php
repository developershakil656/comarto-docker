<?php

namespace App\Http\Requests;

use App\Rules\UniqueReview;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class BusinessReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $decoded = $this->business_id?Hashids::decode($this->business_id):[];

        if (empty($decoded)) {
            // Decoding failed — set business_id as null
            $this->merge(['business_id' => null]);
        } else {
            // Valid — set numeric business_id
            $this->merge(['business_id' => $decoded[0]]);
        }

        
        $parent_id = $this->parent_id?Hashids::decode($this->parent_id):[];
        empty($parent_id)?$this->merge(['parent_id' => null]):$this->merge(['parent_id' => $parent_id[0]]);
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $review_id = $this->route('review')?Hashids::decode($this->route('review')):null;
        return [
            'rating' => 'required|numeric|between:1,5',
            'message' => 'required|string',
            'parent_id' => 'nullable',
            'images' => 'array|nullable',
            'business_id' => [
                'required',
                'integer',
                Rule::when(
                    is_null($this->input('parent_id')), // Apply UniqueReview only if parent_id is null
                    new UniqueReview(Auth::id(), $review_id)
                ),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}
