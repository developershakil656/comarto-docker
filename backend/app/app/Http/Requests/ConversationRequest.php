<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Vinkla\Hashids\Facades\Hashids;

class ConversationRequest extends FormRequest
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
        $user_id = $this->user_id ? Hashids::decode($this->user_id) : [];
        $this->merge([
            'user_id' => is_array($user_id) ? ($user_id[0] ?? null) : null
        ]);

        $business_id = $this->business_id ? Hashids::decode($this->business_id) : [];
        $this->merge([
            'business_id' => is_array($business_id) ? ($business_id[0] ?? null) : null
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'exists:users,id',
            'business_id' => 'exists:businesses,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}
