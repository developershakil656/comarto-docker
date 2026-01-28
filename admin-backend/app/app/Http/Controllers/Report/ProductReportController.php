<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('business');

        // keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                  ->orWhere('id', $keyword)
                  ->orWhere('business_id', $keyword)
                  ->orWhere('slug', 'LIKE', "%$keyword%")
                  ->orWhere('details', 'LIKE', "%$keyword%");
            });
        }

        // date range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // location_id via business
        if ($request->location_id) {
            $query->whereHas('business', function ($q) use ($request) {
                $q->where('location_id', $request->location_id);
            });
        }

        // category_id
        if ($request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // business_id
        if ($request->business_id) {
            $query->where('business_id', $request->business_id);
        }

        // pagination
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return success_response('Products fetched successfully', [
            "data" => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ]
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        if (!in_array($request->status, ['active', 'inactive', 'blocked'])) {
            return error_response('Invalid status value');
        }

        $product = Product::find($id);
        if (!$product) {
            return error_response('Product not found');
        }

        $product->update(['status' => $request->status]);

        return success_response('Product status updated', $product);
    }
}