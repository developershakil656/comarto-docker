<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Vinkla\Hashids\Facades\Hashids;

class ProductRequest extends FormRequest
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
        $category_ids = $this->category_ids
            ? array_map(fn($id) => Hashids::decode($id)[0] ?? null, $this->category_ids)
            : [];
        $category_ids = array_filter($category_ids); // remove nulls
        $this->merge([
            'category_ids' => empty($category_ids) ? null : $category_ids
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
            'name' => 'required',
            'details' => 'required',
            'price' => 'required',
            'moq' => 'nullable|integer',
            'specification' => 'required|array',
            'unit_quantity' => 'integer',
            'product_unit_id' => 'required',
            'video_url' => 'nullable|url',
            'stock' => 'required|in:in stock,out of stock',
            // 'status' => 'required|in:active,inactive',
            'category_ids' => 'required|array|max:3',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(error_response('validation error!', $validator->errors()));
    }
}
