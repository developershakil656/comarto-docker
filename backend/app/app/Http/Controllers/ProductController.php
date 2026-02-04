<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($business = Auth::user()->business) {
            $data = Product::where('business_id', $business->id)->latest()->get();
            return success_response('products fetched.', ProductResource::collection($data));
        } else {
            return error_response('no business created yet!', [], 404);
        }
    }

    // You would install this via composer
    public function store(ProductRequest $request)
    {
        $business = Auth::user()->business;

        if (!$business) {
            return error_response('No business created yet!', [], 404);
        }

        // 1. Create WITHOUT auto-indexing
        $data = $request->all();
        $data['business_id'] = $business->id;
        $data['slug'] = Str::random(8);
        
        // Format price to ensure proper decimal format
        if (isset($data['price'])) {
            $price = $data['price'];
            
            // Check if it's a price range (contains dash)
            if (str_contains($price, '-')) {
                // It's a price range, format both parts
                $priceParts = explode('-', $price);
                $minPrice = floatval(trim($priceParts[0]));
                $maxPrice = floatval(trim($priceParts[1]));
                
                // Only add decimal places if there are actual decimal values
                $minPriceFormatted = floor($minPrice) == $minPrice ? (int)$minPrice : number_format($minPrice, 2, '.', '');
                $maxPriceFormatted = floor($maxPrice) == $maxPrice ? (int)$maxPrice : number_format($maxPrice, 2, '.', '');
                
                $data['price'] = $minPriceFormatted . ' - ' . $maxPriceFormatted;
            } else {
                // It's a single price, only add decimal places if there are actual decimal values
                $priceFloat = floatval($price);
                $data['price'] = floor($priceFloat) == $priceFloat ? (int)$priceFloat : number_format($priceFloat, 2, '.', '');
            }
        }

        $product = Product::create(Arr::except($data, ['category_ids']));

        // 2. Final slug
        $hash = Hashids::encode($product->id);
        $product->update([
            'slug' => Str::slug($request->name) . '-' . $hash,
        ]);

        // 3. Categories
        $product->categories()->sync($data['category_ids'] ?? []);

        // 4. Hard refresh (CRITICAL)
        $product->refresh();

        // 5. Load relations
        $product->load(['categories', 'business.location', 'business.details']);

        // 6. Manual Scout indexing
        $product->searchable();

        return success_response('Product created', new ProductResource($product));
    }



    /**
     * Display a listing of the resource.
     */
    public function show($id)
    {
        $decoded = $id ? Hashids::decode($id) : [];
        if ($business = Auth::user()->business) {
            $data = Product::where([
                ['business_id', $business->id],
                ['id', $decoded[0]]
            ])->first();
            return success_response('product fetched.', new ProductResource($data));
        } else {
            return error_response('no business created yet!', [], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $decoded = Hashids::decode($id);
        $product = Product::findOrFail($decoded[0]);

        Gate::authorize('update', $product);

        $data = $request->except(['slug']);
        
        // Format price to ensure proper decimal format
        if (isset($data['price'])) {
            $price = $data['price'];
            
            // Check if it's a price range (contains dash)
            if (str_contains($price, '-')) {
                // It's a price range, format both parts
                $priceParts = explode('-', $price);
                $minPrice = floatval(trim($priceParts[0]));
                $maxPrice = floatval(trim($priceParts[1]));
                
                // Only add decimal places if there are actual decimal values
                $minPriceFormatted = floor($minPrice) == $minPrice ? (int)$minPrice : number_format($minPrice, 2, '.', '');
                $maxPriceFormatted = floor($maxPrice) == $maxPrice ? (int)$maxPrice : number_format($maxPrice, 2, '.', '');
                
                $data['price'] = $minPriceFormatted . ' - ' . $maxPriceFormatted;
            } else {
                // It's a single price, only add decimal places if there are actual decimal values
                $priceFloat = floatval($price);
                $data['price'] = floor($priceFloat) == $priceFloat ? (int)$priceFloat : number_format($priceFloat, 2, '.', '');
            }
        }

        $product->update(Arr::except($data, ['category_ids']));

        $product->categories()->sync($data['category_ids'] ?? []);

        // IMPORTANT: reload relations
        $product->load(['categories', 'business.location', 'business.details']);

        // Force Scout re-index with fresh data
        $product->searchable();

        return success_response(
            'Product successfully updated',
            new ProductResource($product)
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Decode the hashed product ID
        $decoded = $id ? Hashids::decode($id) : [];

        // Check if the decoded ID is valid
        if (empty($decoded[0])) {
            return error_response('Invalid product ID', [], 400);
        }

        // Retrieve the product using the decoded ID
        $data = Product::find($decoded[0]);

        // Check if the product exists
        if (!$data) {
            return error_response('Product not found!', [], 404);
        }

        // Authorize that the user has permission to delete the product
        Gate::authorize('delete', $data);

        // Get the product's gallery images
        $gallery = $data->gallery;

        // Try to delete the product and its associated gallery images
        if ($data->delete()) {
            if ($gallery) {
                foreach ($gallery as $img) {
                    delete_image($img->url);
                }
            }

            // Return success response if the product and its gallery images were deleted successfully
            return success_response('Product successfully deleted');
        }

        // If the deletion of the product failed, return an error
        return error_response('Failed to delete the product', [], 500);
    }
}
