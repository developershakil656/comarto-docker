<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewGalleryRequest;
use App\Models\BusinessReview;
use App\Models\ReviewGallery;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class ReviewGalleryController extends Controller
{
    public function store_images($review_id, $images)
    {
        // Find the review by ID
        $review = BusinessReview::find($review_id);

        // Return error if review doesn't exist or isn't authorized
        if (!$review || ($review->user_id != Auth::id())) {
            return error_response('No review found!', [], 404);
        }

        foreach ($images as $image) {
            // Generate the image URL/path using the updated helper function
            $imagePath = image_link_generator($image, '/business/review/', $review_id);

            // Save the image record for the review gallery
            ReviewGallery::create([
                'business_review_id' => $review_id,
                'url' => $imagePath,
            ]);

        }

        return success_response('Images successfully stored');
    }

    #update gallery
    public function update_images($review_id, $gallery_data)
    {
        // Deleting gallery images if "delete" data is provided
        if (!empty($gallery_data['delete'])) {
            foreach ($gallery_data['delete'] as $id) {
                $id = Hashids::decode($id)[0] ?? null;
                $gallery = ReviewGallery::find($id);

                if ($gallery) {
                    delete_image($gallery->url);
                    // Delete the gallery entry
                    $gallery->delete();
                }
            }
        }

        // Adding new images if "add" data is provided
        if (!empty($gallery_data['add'])) {
            $this->store_images($review_id, $gallery_data['add']);
        }
    }
}
