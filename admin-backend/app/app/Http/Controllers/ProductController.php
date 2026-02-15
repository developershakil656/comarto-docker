<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductGalleryResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('business', 'categories');

        // Keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                  ->orWhere('slug', 'LIKE', "%$keyword%")
                  ->orWhere('details', 'LIKE', "%$keyword%");
            });
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Stock filter
        if ($request->stock) {
            $query->where('stock', $request->stock);
        }

        // Business filter
        if ($request->business_id) {
            $query->where('business_id', $request->business_id);
        }

        // Category filter
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Date range filter
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Pagination
        $perPage = $request->per_page ?? 15;
        $products = $query->latest()->paginate($perPage);

        return success_response('Products fetched successfully', [
            'data' => ProductResource::collection($products),
            'meta' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }
    
    /**
     * Store a newly created product (admin creates)
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->all();
            
            // Generate slug
            $data['slug'] = Str::random(8);
            
            // Set default status to active if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }
            
            // Format price
            if (isset($data['price'])) {
                $price = $data['price'];
                
                if (str_contains($price, '-')) {
                    $priceParts = explode('-', $price);
                    $minPrice = floatval(trim($priceParts[0]));
                    $maxPrice = floatval(trim($priceParts[1]));
                    
                    $minPriceFormatted = floor($minPrice) == $minPrice ? (int)$minPrice : number_format($minPrice, 2, '.', '');
                    $maxPriceFormatted = floor($maxPrice) == $maxPrice ? (int)$maxPrice : number_format($maxPrice, 2, '.', '');
                    
                    $data['price'] = $minPriceFormatted . ' - ' . $maxPriceFormatted;
                } else {
                    $priceFloat = floatval($price);
                    $data['price'] = floor($priceFloat) == $priceFloat ? (int)$priceFloat : number_format($priceFloat, 2, '.', '');
                }
            }
            
            // Create product without category_ids yet
            $product = Product::create(Arr::except($data, ['category_ids']));
            
            // Update slug with hash
            $hash = Str::random(8);
            $product->update([
                'slug' => Str::slug($request->name) . '-' . $hash,
            ]);
            
            // Sync categories if provided
            if (isset($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }
            
            // Load relations
            $product->load(['categories', 'business']);
            
            // Index in Meilisearch if available
            if (method_exists($product, 'searchable')) {
                $product->searchable();
            }
            
            return success_response('Product created successfully', new ProductResource($product), 201);
            
        } catch (\Exception $e) {
            return error_response('Failed to create product: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Display a specific product
     */
    public function show($id)
    {
        try {
            $product = Product::with('business', 'categories', 'gallery')->findOrFail($id);
            return success_response('Product fetched successfully', new ProductResource($product));
        } catch (\Exception $e) {
            return error_response('Product not found', [], 404);
        }
    }

    /**
     * Update the specified product
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $data = $request->except(['slug']);
            
            // Format price
            if (isset($data['price'])) {
                $price = $data['price'];
                
                if (str_contains($price, '-')) {
                    $priceParts = explode('-', $price);
                    $minPrice = floatval(trim($priceParts[0]));
                    $maxPrice = floatval(trim($priceParts[1]));
                    
                    $minPriceFormatted = floor($minPrice) == $minPrice ? (int)$minPrice : number_format($minPrice, 2, '.', '');
                    $maxPriceFormatted = floor($maxPrice) == $maxPrice ? (int)$maxPrice : number_format($maxPrice, 2, '.', '');
                    
                    $data['price'] = $minPriceFormatted . ' - ' . $maxPriceFormatted;
                } else {
                    $priceFloat = floatval($price);
                    $data['price'] = floor($priceFloat) == $priceFloat ? (int)$priceFloat : number_format($priceFloat, 2, '.', '');
                }
            }
            
            // Update product
            $product->update(Arr::except($data, ['category_ids']));
            
            // Sync categories if provided
            if (isset($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }
            
            // Reload relations
            $product->load(['categories', 'business']);
            
            // Reindex in Meilisearch if available
            if (method_exists($product, 'searchable')) {
                $product->searchable();
            }
            
            return success_response('Product updated successfully', new ProductResource($product));
            
        } catch (\Exception $e) {
            return error_response('Failed to update product: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Delete the specified product
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete associated gallery images
            if ($product->gallery) {
                foreach ($product->gallery as $img) {
                    if (method_exists($img, 'delete')) {
                        delete_image($img->url);
                    }
                    $img->delete();
                }
            }
            
            // Delete product
            $product->delete();
            
            return success_response('Product deleted successfully');
            
        } catch (\Exception $e) {
            return error_response('Failed to delete product: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Upload product gallery images
     */
    public function uploadImages(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Validate images
            $validator = Validator::make($request->all(), [
                'images.*' => 'required|image|max:5120'
            ]);
            
            if ($validator->fails()) {
                return error_response('Image validation failed', $validator->errors(), 422);
            }
            
            if (!$request->hasFile('images')) {
                return error_response('No images provided', [], 400);
            }
            
            $uploadedImages = [];
            
            foreach ($request->file('images') as $image) {
                try {
                    // Use helper function to generate image URL
                    $imagePath = image_link_generator($image, 'product/gallery/', $product->name);
                    
                    // Create gallery record
                    $gallery = ProductGallery::create([
                        'product_id' => $product->id,
                        'url' => $imagePath
                    ]);
                    
                    $uploadedImages[] = $gallery;
                    
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            if (empty($uploadedImages)) {
                return error_response('Failed to upload images', [], 500);
            }
            
            return success_response(
                'Images uploaded successfully',
                [
                    'images' => ProductGalleryResource::collection($uploadedImages),
                    'count' => count($uploadedImages)
                ]
            );
            
        } catch (\Exception $e) {
            return error_response('Failed to process request: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Delete product gallery image
     */
    public function deleteImage($imageId)
    {
        try {
            $image = ProductGallery::findOrFail($imageId);
            
            // Delete file
            if (method_exists($image, 'delete')) {
                delete_image($image->url);
            }
            
            // Delete record
            $image->delete();
            
            return success_response('Image deleted successfully');
            
        } catch (\Exception $e) {
            return error_response('Failed to delete image: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Reorder product gallery images
     */
    public function reorderImages(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $order = $request->input('image_ids', []);
            if (!is_array($order)) {
                return error_response('Invalid payload', [], 400);
            }

            foreach ($order as $item) {
                if (!isset($item['id'])) continue;
                $img = ProductGallery::where('product_id', $productId)
                        ->where('id', $item['id'])
                        ->first();
                if ($img) {
                    $img->serial = isset($item['position']) ? $item['position'] : 0;
                    $img->save();
                }
            }

            return success_response('Image positions updated successfully');
        } catch (\Exception $e) {
            return error_response('Failed to reorder images: ' . $e->getMessage(), [], 500);
        }
    }
}
