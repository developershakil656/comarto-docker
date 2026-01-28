<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadProposalResource;
use App\Http\Resources\LeadResource;
use App\Models\BusinessLead;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeadReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        // keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('description', 'LIKE', "%$keyword%")
                    ->orWhere('id', $keyword)
                    ->orWhere('user_id', $keyword)
                    ->orWhere('business_id', $keyword);
            });
        }

        // date-range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // location_id
        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        // category_id
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // pagination
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $leads = $query->paginate($perPage, ['*'], 'page', $page);

        return success_response('users fetched successfully', [
            "data" => LeadResource::collection($leads),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page'    => $leads->lastPage(),
                'per_page'     => $leads->perPage(),
                'total'        => $leads->total(),
            ]
        ]);
    }

    public function proposals($id){
        $proposals = BusinessLead::where('lead_id', $id)->get();

        return success_response('proposals fetched successfully', LeadProposalResource::collection($proposals));
    }
}
