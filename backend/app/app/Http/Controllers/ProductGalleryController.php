<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductGalleryRequest;
use App\Http\Resources\ProductGalleryResource;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Vinkla\Hashids\Facades\Hashids;

class ProductGalleryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductGalleryRequest $request)
    {
        // Decode product ID
        $product_id = $request->product_id ? Hashids::decode($request->product_id) : [];
        $request['product_id'] = $product_id[0];

        // Find the associated product
        $data = Product::find($request->product_id);

        // Send error if product is not found or if it doesn't belong to the current user's business
        if (!$data || ($data->business->id != Auth::user()->business->id)) {
            return error_response('No product found!', [], 404);
        }

        // Get the last serial number for the product gallery
        $lastSerial = ProductGallery::where('product_id', $request->product_id)->max('serial') ?? 0;

        // Store the new image and retrieve its URL
        // We now use the updated image_link_generator function
        $imagePath = image_link_generator($request->image, '/product/gallery/', $data->name);

        // Prepare the data to be inserted
        $request['url'] = $imagePath;  // Image URL
        $request['serial'] = $lastSerial + 1;
        $gallery = ProductGallery::create($request->all());

        return success_response('Product image successfully uploaded',new ProductGalleryResource($gallery));
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
                    DB::table('product_galleries')
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
        // Decode the provided ID
        $decoded = $id ? Hashids::decode($id) : [];
        $data = ProductGallery::find($decoded[0]);

        // Check if the data exists and belongs to the current user's business
        if ($data && ($data->product->business->id == Auth::user()->business->id)) {
            delete_image($data->url);

            // Delete the gallery record
            $data->delete();

            return success_response('Image successfully deleted');
        } else {
            return error_response('No image found or unauthorized action!', [], 404);
        }
    }
}
