<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\CategoryResource;
use App\Models\Business;
use App\Models\BusinessDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Business::where('user_id', Auth::id())->first();
        if ($data)
            return success_response('businesses fetched.', new BusinessResource($data));
        else
            return error_response('no business created!', [], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessRequest $request)
    {
        if (Auth::user()->business) {
            return error_response('You have already created a business!');
        }

        // $request = $request->all();
        $request['user_id'] = Auth::user()->id;

        if ($data = Business::create($request->except('timings'))) {
            BusinessDetail::create(['business_id' => $data->id, 'timing' => ($request->timings ?? null)]);
            return success_response('business successfully created', new BusinessResource($data));
        }

        return error_response('something went wrong!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessRequest $request, string $id)
    {
        $data = Business::where('user_id', Auth::id())->first();
        if ($data) {
            // $data->update(array_filter(
            //     $request->all(),
            //     fn($value) => !is_null($value) && $value !== ''
            // ));
            $data->update($request->except('slug'));
            return success_response('business successfully updated', new BusinessResource($data));
        } else
            return error_response('No Data Found!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the business record associated with the user
        $data = Business::where('user_id', Auth::id())->first();

        // Check if business exists and belongs to the logged-in user
        if ($data) {
            // Attempt to delete the business
            if ($data->delete()) {
                delete_image($data->business_profile);
                return success_response('Business successfully deleted');
            } else {
                return error_response('Failed to delete business', [], 500);
            }
        } else {
            return error_response('No business found for this user!', [], 404);
        }
    }

    #business profile picture update
    public function profile(Request $request)
    {
        // Validate uploaded file
        $validator = Validator::make($request->all(), [
            'business_profile' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return error_response('validation error!', $validator->errors());
        }

        // Get business for authenticated user
        $data = Business::where('user_id', Auth::id())->first();

        // Send error if not found
        if (!$data) {
            return error_response('No Data Found!', [], 404);
        }

        // Remove existing image if exists
        delete_image($data->business_profile);

        // Store new image using multipart upload
        if ($request->hasFile('business_profile')) {
            $file_url = image_link_generator(
                $request->file('business_profile'),
                '/business/profile/',
                $data->business_name,
                300
            );

            // Save new file URL
            $data->business_profile = $file_url;
            $data->save();
        }

        return success_response('Business profile successfully updated', new BusinessResource($data));
    }

    public function categories()
    {
        $categories = Auth::user()->business->products
            ->flatMap(fn($product) => $product->categories) // keep as models
            ->unique('id')                                  // filter duplicates
            ->values();

        return success_response(
            'business categories fetched successfully',
            CategoryResource::collection($categories)
        );
    }

    public function checkSlugAvailability(Request $request)
    {
        // 2. Create the validator
        $validator = Validator::make($request->all(), [
            'slug' => 'required|unique:businesses,slug|alpha_dash|min:3|max:50',
        ], [
            'slug.unique' => 'This business business-slug is already taken.',
            'slug.alpha_dash' => 'The business business-slug can only contain letters, numbers, and dashes.',
            'slug.min' => 'The business business-slug must be at least 3 characters long.',
            'slug.max' => 'The business business-slug must not be greater than 50 characters.',
        ]);

        // 3. Check if validation fails
        if ($validator->fails()) {
            return error_response('validation error!', $validator->errors());
        }

        // 4. If valid, return success
        return success_response('this business-slug is available');
    }
}
