<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessReviewRequest;
use App\Http\Resources\BusinessReviewResource;
use App\Http\Resources\UserBusinessReviewResource;
use App\Models\BusinessReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class BusinessReviewController extends Controller
{
    /**
 * Display a listing of the resource.
 */
public function index(Request $request)
{
    // Default pagination values
    $page = (int) $request->get('page', 1);
    $perPage = (int) $request->get('per_page', 10);

    $data = BusinessReview::where([
            ['user_id', Auth::id()],
            ['parent_id', null]
        ])
        ->paginate($perPage, ['*'], 'page', $page);

    return success_response('reviews fetched.', [
        'data' => UserBusinessReviewResource::collection($data),
        'meta' => [
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
        ]
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessReviewRequest $request)
    {
        $request['user_id'] = Auth::user()->id;

        if ($data = BusinessReview::create($request->except('images'))) {
            if ($request['images']) {
                $gallery = new ReviewGalleryController;
                $gallery->store_images($data->id, $request['images']);
            }
        }
        return success_response('review successfully created', new BusinessReviewResource($data));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessReviewRequest $request, string $id)
    {
        $id = Hashids::decode($id)[0] ?? null;
        $data = BusinessReview::where([
            ['user_id', Auth::id()],
            ['id', $id]
        ])->first();
        if ($data && $data->update($request->except('business_id', 'parent_id', 'images'))) {
            if ($request['images']) {
                $gallery = new ReviewGalleryController;
                $gallery->update_images($data->id, $request['images']);
            }
            return success_response('review successfully updated', new BusinessReviewResource($data));
        } else {
            return error_response('No Data Found!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $business_id)
    {
        // Decode the hash and retrieve the business review by ID
        $business_id = Hashids::decode($business_id)[0] ?? null;

        // Fetch the review based on user ID and business review ID
        $data = BusinessReview::where([
            ['user_id', Auth::id()],
            ['id', $business_id]
        ])->first();

        // Check if the review exists
        if ($data) {
            // Process each reply to the review
            foreach ($data->replies as $reply) {
                $gallery = $reply->gallery;

                // Delete each reply's gallery images
                if ($reply->delete()) {
                    foreach ($gallery as $img) {
                        delete_image($img->url);
                    }
                }
            }

            // Process and delete the main review's gallery images
            $gallery = $data->gallery;

            // Delete the main review itself
            if ($data->delete()) {
                // Delete the gallery images associated with the review
                foreach ($gallery as $img) {
                    delete_image($img->url);
                }

                return success_response('Review successfully removed');
            } else {
                return error_response('Failed to delete review', [], 500);
            }
        } else {
            // If no review found for the logged-in user
            return error_response('No review found for this business!', [], 404);
        }
    }
}
