<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Vinkla\Hashids\Facades\Hashids;

class ReviewGalleryRequest extends FormRequest
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
        $decoded = $this->review_id? Hashids::decode($this->review_id):[];

        if (empty($decoded)) {
            $this->merge(['review_id' => null]);
        } else {
            $this->merge(['review_id' => $decoded[0]]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'image' => 'required|image|mimes:jpeg,jpg,png,webp',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            'review_id' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}
