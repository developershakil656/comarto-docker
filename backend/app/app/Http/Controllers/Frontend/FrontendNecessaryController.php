<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessDetailResource;
use App\Http\Resources\BusinessProductResource;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\BusinessReviewResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ChildrenCategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SearchProductResource;
use App\Http\Resources\ShortProductResource;
use App\Models\Business;
use App\Models\BusinessDetail;
use App\Models\BusinessReview;
use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class FrontendNecessaryController extends Controller
{
    #search active locations
    public function locations(Request $request)
    {
        $locations = Location::search($request->keyword)
            ->options([
                'limit' => 15,
                'offset' => 0,
                'filter' => ['(status = "active")']
            ])->get();
        return success_response('locations fetched', LocationResource::collection($locations));
    }

    #search active business types
    public function business_types()
    {
        $types = BusinessType::where('status', 'active')->pluck('type')->toArray();
        return success_response('business types fetched', $types);
    }

    #search active categories for business
    public function categories(Request $request)
    {
        $query = Category::whereNull('parent_id')
            ->where('status', 'active');

        if ($request->has('limit') && is_numeric($request->limit)) {
            $query->limit($request->limit);
        }

        $data = $query->get();

        return success_response('categories fetched', CategoryResource::collection($data));
    }

    #search active children categories for business
    public function category_children($slug)
    {
        $data = Category::where('slug', $slug)
            ->where('status', 'active')
            ->with('children')
            ->first();
        if ($data)
            return success_response('categories fetched', new ChildrenCategoryResource($data));
    }

    #search active products
    public function search(Request $request)
    {
        $types = $request->business_types;
        $filters = ['status = "active"', 'stock = "in stock"'];

        if (!empty($types)) {
            $types = explode(',', $types);
            $typeFilters = collect($types)
                ->map(fn($type) => 'business_type = "' . $type . '"')
                ->implode(' OR ');
            $filters[] = '(' . $typeFilters . ')';
        }

        if ($request->location) {
            $location = explode(',', $request->location);
            $upazila_name = isset($location[1]) ? $location[0] : '';
            $district_name = isset($location[1]) ? $location[1] : $location[0];

            if ($upazila_name) {
                $filters[] = '(upazila_name = "' . $upazila_name . '")';
            }

            if ($district_name) {
                $filters[] = '(district_name = "' . $district_name . '")';
            }
        }

        // 🔹 Category filter (decode Hashids here)
        $category_slugs = $request->category_slugs;
        if (!empty($category_slugs)) {
            $categorySlugs = explode(',', $category_slugs);

            if (!empty($categorySlugs)) {
                $categoryFilters = collect($categorySlugs)
                    ->map(fn($slug) => 'related_categories = "' . $slug . '"')
                    ->implode(' OR ');
                $filters[] = '(' . $categoryFilters . ')';
            }
        }

        // Default pagination values
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        if ($request->suppliers) {
            // Get all products and make unique by business_id
            $allProducts = Product::search($request->keyword)
                ->options(['filter' => $filters])
                ->get();

            $uniqueSuppliers = collect($allProducts)->unique('business_id')->values();

            // Manual pagination
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $uniqueSuppliers->forPage($page, $perPage),
                $uniqueSuppliers->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return success_response('suppliers fetched', [
                'data' => SearchProductResource::collection($paginated),
                'meta' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ]
            ]);
        }

        // Normal product search with pagination
        $products = Product::search($request->keyword)
            ->options(['filter' => $filters])
            ->paginate($perPage, 'page', $page);

        return success_response('products fetched', [
            'data' => SearchProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    #single product
    public function product($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if ($product) {
            $data = [];
            $data['product'] = new ProductResource($product);
            $data['business'] = new BusinessResource($product->business);
            $data['business_details'] = new BusinessDetailResource($product->business->details);

            return success_response('Product fetched', $data);
        } else {
            return error_response('product not found!', [], 404);
        }
    }

    public function business_reviews($hashed_id, Request $request)
    {
        // 1. Decode and Validate ID
        $business_id = Hashids::decode($hashed_id)[0] ?? abort(404, 'Business not found');

        // 2. Fetch Business (to get rating and ensure it exists)
        $business = Business::findOrFail($business_id);

        // 3. Paginate Reviews with Eager Loading
        $reviews = BusinessReview::with(['user', 'business']) // Eager load relationships
            ->where('business_id', $business_id)
            ->whereNull('parent_id')
            ->latest() // Usually reviews should be newest first
            ->paginate($request->integer('per_page', 10));

        // 4. Transform using Resource (Laravel handles meta/links automatically)
        $resource = BusinessReviewResource::collection($reviews);

        return success_response('Reviews fetched.', [
            'rating' => $business->averageRating ?? 0,
            'data'   => $resource->response()->getData()->data,
            'meta'   => $resource->response()->getData()->meta,
        ]);
    }

    public function more_products_from_seller($business_id, $except_product_id = null)
    {
        $business_id = Hashids::decode($business_id)[0] ?? null;
        $except_product_id = $except_product_id ? Hashids::decode($except_product_id)[0] : null;

        $query = Product::where([
            ['business_id', $business_id],
            ['status', 'active'],
            ['stock', 'in stock']
        ]);

        if ($except_product_id) {
            $query->where('id', '!=', $except_product_id);
        }

        $products = $query->take(15)->get();

        return success_response('Products fetched.', ShortProductResource::collection($products));
    }

    public function related_products($product_id)
    {
        $product_id = Hashids::decode($product_id)[0] ?? null;
        $product = Product::find($product_id);
        $category_ids = $product->categories->pluck('id')->toArray();

        // Get related products through the pivot table
        $products = Product::whereHas('categories', function ($q) use ($category_ids) {
            $q->whereIn('categories.id', $category_ids);
        })
            ->where('id', '!=', $product->id) // exclude the current product
            ->take(15)
            ->get();

        return success_response('related products fetched.', ShortProductResource::collection($products));
    }

    public function related_categories($product_id)
    {
        $product_id = Hashids::decode($product_id)[0] ?? null;
        $product = Product::find($product_id);
        $parent_category_id = $product->categories->first()->parent_id;
        // 
        $categories = Category::where('parent_id', $parent_category_id)->take(15)->get();

        return success_response('related categories fetched.', CategoryResource::collection($categories));
    }

    public function business($business_slug)
    {
        $business = Business::where('slug', $business_slug)->first();

        return success_response('business fetched', new BusinessResource($business));
    }

    public function business_details($business_id)
    {
        $business_id = Hashids::decode($business_id)[0] ?? null;
        $business_details = BusinessDetail::where('business_id', $business_id)->first();

        return success_response('business details fetched', new BusinessDetailResource($business_details));
    }

    public function business_products($business_id)
    {
        $business_id = Hashids::decode($business_id)[0] ?? null;

        $products = Product::where([
            ['business_id', $business_id],
            ['status', 'active'],
            ['stock', 'in stock']
        ])->get();

        return success_response('business products fetched.', BusinessProductResource::collection($products));
    }
}
