<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessUtilityController extends Controller
{
    /**
     * Check if a business slug is available
     */
    public function checkSlugAvailability(Request $request)
    {
        // 2. Create the validator
        $validator = Validator::make($request->all(), [
            'slug' => 'required|unique:businesses,slug|alpha_dash|min:3|max:50',
        ], [
            'slug.unique' => 'This business slug is already taken.',
            'slug.alpha_dash' => 'The business slug can only contain letters, numbers, and dashes.',
            'slug.min' => 'The business slug must be at least 3 characters long.',
            'slug.max' => 'The business slug must not be greater than 50 characters.',
        ]);

        // 3. Check if validation fails
        if ($validator->fails()) {
            return error_response('validation error!', $validator->errors());
        }

        // 4. If valid, return success
        return success_response('this business slug is available');
    }
}