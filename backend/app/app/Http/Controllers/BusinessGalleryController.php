<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessGalleryRequest;
use App\Http\Resources\BusinessGalleryResource;
use App\Models\BusinessGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BusinessGalleryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessGalleryRequest $request)
    {
        $business = Auth::user()->business;

        // Get the last serial number of this product gallery
        $lastSerial = BusinessGallery::where('business_id', $business->id)->max('serial') ?? 0;

        // Store new image and retrieve its URL
        // Adjust the path as necessary to match your directory structure
        $imagePath = image_link_generator($request->image, '/business/gallery/', $business->business_name, 0, false);

        // Prepare the data to be stored in the database
        $data = [
            'url' => $imagePath, // The generated image URL
            'serial' => $lastSerial + 1,
            'business_id' => $business->id,
        ];
        BusinessGallery::create($data);
        // Return success response with the new gallery resource
        return success_response('Business image successfully uploaded');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'image_ids' => 'required|array',
            'image_ids.*.id' => 'required|string',
            'image_ids.*.position' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return error_response('Validation error!', $validator->errors());
        }

        DB::transaction(function () use ($request) {
            foreach ($request->image_ids as $item) {
                $decodedId = Hashids::decode($item['id']);
                if (!empty($decodedId)) {
                    DB::table('business_galleries')
                        ->where('id', $decodedId[0])
                        ->update(['serial' => $item['position']]);
                }
            }
        });

        return success_response('Images successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $decoded = $id ? Hashids::decode($id) : [];
        $data = BusinessGallery::find($decoded[0]);

        // Check if the data exists, belongs to the authenticated user's business, and delete it
        if ($data && ($data->business_id == Auth::user()->business->id)) {
            // Delete the file if it exists
            delete_image($data->url);

            // Delete the gallery entry
            $data->delete();

            return success_response('Image successfully deleted');
        } else {
            return error_response('No image found or unauthorized action!', [], 404);
        }
    }
}
