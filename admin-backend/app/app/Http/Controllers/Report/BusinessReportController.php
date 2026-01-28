<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\ProductResource;
use App\Models\Business;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusinessReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::query();

        // keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('id', $keyword)
                    ->orWhere('user_id', $keyword)
                    ->orWhere('phone', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('address', 'LIKE', "%$keyword%");
            });
        }

        // date range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // location_id
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // business_type (array)
        if ($request->business_type) {
            $types = explode(",", $request->business_type);

            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    $q->orWhereRaw(
                        "JSON_SEARCH(LOWER(business_type), 'one', ?) IS NOT NULL",
                        [strtolower(trim($type))]
                    );
                }
            });
        }

        // pagination
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $businesses = $query->paginate($perPage, ['*'], 'page', $page);

        return success_response('Businesses fetched successfully', [
            "data" => BusinessResource::collection($businesses),
            'meta' => [
                'current_page' => $businesses->currentPage(),
                'last_page'    => $businesses->lastPage(),
                'per_page'     => $businesses->perPage(),
                'total'        => $businesses->total(),
            ]
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        if (!in_array($request->status, ['active', 'inactive', 'blocked'])) {
            return error_response('Invalid status. Must be active, inactive, or blocked');
        }

        $business = Business::find($id);
        if (!$business) {
            return error_response('Business not found');
        }

        $business->update(['status' => $request->status]);

        return success_response('Business status updated successfully', $business);
    }

}
