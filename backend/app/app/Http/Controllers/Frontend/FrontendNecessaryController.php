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
    public function category_children($path)
    {
        // 1. Clean and split the path
        $slugArray = explode('/', rtrim($path, '/'));
        $targetSlug = end($slugArray);

        // 2. Query using whereJsonContains (The most reliable way for JSON columns)
        $category = Category::where('slug', $targetSlug)
            ->where('status', 'active')
            ->whereJsonContains('ancestor_slugs', $slugArray)
            ->with(['children' => fn($q) => $q->where('status', 'active')])
            ->first();

        // 3. Final Verification: Ensure the URL path length matches the DB depth
        // This prevents "agriculture/rice" from matching "agriculture"
        if (!$category || count($category->ancestor_slugs) !== count($slugArray)) {
            return response()->json(['message' => 'Invalid category path'], 404);
        }

        return success_response('Fetched', new ChildrenCategoryResource($category));
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
        $category_slug = $request->category_slug;
        if ($category_slug) {
            $filters[] = '(related_categories = "' . $category_slug . '")';
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

    #random products
    public function randomProducts(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $page = max((int) $request->get('page', 1), 1);
        $offset = ($page - 1) * $perPage;

        $locationParam = $request->location;

        $upazila = '';
        $district = '';

        if ($locationParam) {
            $location = explode(',', $locationParam);
            $upazila = isset($location[1]) ? trim($location[0]) : '';
            $district = isset($location[1]) ? trim($location[1]) : trim($location[0]);
        }

        $baseFilters = [
            'status = "active"',
            'stock = "in stock"',
        ];

        $collectedIds = [];

        /** -----------------------------
         * 1️⃣ Upazila priority
         * ----------------------------- */
        if ($upazila) {
            $upazilaHits = Product::search(null)
                ->options([
                    'filter' => array_merge($baseFilters, ['upazila_name = "' . $upazila . '"']),
                    'sort' => ['random_sort_key:asc'],
                    'offset' => $offset,
                    'limit' => $perPage,
                    'attributesToRetrieve' => ['id'],
                ])
                ->raw()['hits'] ?? [];

            $collectedIds = array_merge($collectedIds, collect($upazilaHits)->pluck('id')->toArray());
        }

        $used = count($collectedIds);

        /** -----------------------------
         * 2️⃣ District fallback
         * ----------------------------- */
        if ($used < $perPage && $district) {
            $districtOffset = max(0, $offset - $used);

            $districtHits = Product::search(null)
                ->options([
                    'filter' => array_merge(
                        $baseFilters,
                        [
                            'district_name = "' . $district . '"',
                            $upazila ? 'upazila_name != "' . $upazila . '"' : ''
                        ]
                    ),
                    'sort' => ['random_sort_key:asc'],
                    'offset' => $districtOffset,
                    'limit' => $perPage - $used,
                    'attributesToRetrieve' => ['id'],
                ])
                ->raw()['hits'] ?? [];

            $collectedIds = array_merge($collectedIds, collect($districtHits)->pluck('id')->toArray());
            $used = count($collectedIds);
        }

        /** -----------------------------
         * 3️⃣ Other locations
         * ----------------------------- */
        if ($used < $perPage) {
            $otherOffset = max(0, $offset - $used);

            $otherFilters = array_filter([
                $upazila ? 'upazila_name != "' . $upazila . '"' : null,
                $district ? 'district_name != "' . $district . '"' : null,
            ]);

            $otherHits = Product::search(null)
                ->options([
                    'filter' => array_merge($baseFilters, $otherFilters),
                    'sort' => ['random_sort_key:asc'],
                    'offset' => $otherOffset,
                    'limit' => $perPage - $used,
                    'attributesToRetrieve' => ['id'],
                ])
                ->raw()['hits'] ?? [];

            $collectedIds = array_merge($collectedIds, collect($otherHits)->pluck('id')->toArray());
        }

        /** -----------------------------
         * 4️⃣ Fetch Eloquent models in the same order
         * ----------------------------- */
        $products = Product::whereIn('id', $collectedIds)
            ->with(['business.location', 'business.details', 'categories']) // eager load relations
            ->orderByRaw("FIELD(id, " . implode(',', $collectedIds) . ")")
            ->get();

        return success_response('Products fetched', [
            'data' => SearchProductResource::collection($products),
            'meta' => [
                'current_page' => $page,
                'last_page' => $products->count() < $perPage ? $page : $page + 1,
                'per_page' => $perPage,
                'total' => $perPage,
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
