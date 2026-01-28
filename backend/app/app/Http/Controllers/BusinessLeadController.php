<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusinessLeadResource;
use App\Http\Resources\LeadResource;
use App\Http\Resources\LeadUserResource;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\BusinessLead;
use App\Services\LeadCreditService;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class BusinessLeadController extends Controller
{
    protected $creditService;

    public function __construct(LeadCreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    // List/filter available leads
    public function index(Request $request)
    {
        $filters = ['(user_id != "' . Auth::id() . '")', '(status = "open")'];

        if ($request->location) {
            $location = explode(',', $request->location);
            $upazila_name = isset($location[1]) ? $location[0] : '';
            $district_name = isset($location[1]) ? $location[1] : $location[0];

            if ($upazila_name)
                $filters[] = '(upazila_name = "' . $upazila_name . '")';

            if ($district_name)
                $filters[] = '(district_name = "' . $district_name . '")';

            // $filters[] = '(upazila_name = "' . $upazila_name . '" OR district_name = "' . $district_name . '")';
        }

        // 🔹 Category filter (decode Hashids here)
        $category_ids = $request->category_ids;
        if (!empty($category_ids)) {
            $categoryIds = explode(',', $category_ids);

            $decodedIds = collect($categoryIds)
                ->map(fn($id) => Hashids::decode($id)[0] ?? null) // decode each
                ->filter() // remove nulls
                ->all();

            if (!empty($decodedIds)) {
                $categoryFilters = collect($decodedIds)
                    ->map(fn($id) => 'category.id = "' . $id . '"')
                    ->implode(' OR ');
                $filters[] = '(' . $categoryFilters . ')';
            }
        }

        $sortOption = null;
        if ($request->sort_by == 'recent') {
            $sortOption = ['updated_at:desc']; // latest first
        }

        // Default pagination values
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $leads = Lead::search($request->keyword)
            ->options([
                'filter' => $filters,
                'sort'   => $sortOption,
            ])
            ->paginate($perPage, 'page', $page);

        return success_response('Leads fetched successfully', [
            "data" => LeadResource::collection($leads),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ]
        ]);
    }

    // Unlock a lead using LeadCreditService
    public function unlock($leadId)
    {
        $leadId = $leadId ? Hashids::decode($leadId)[0] : [];
        $business = auth()->user()->business;
        $lead = Lead::findOrFail($leadId);

        if ($lead->user_id === auth()->id()) {
            return error_response("You cannot unlock your own inquiry!");
        }

        if (BusinessLead::where('business_id', $business->id)->where('lead_id', $lead->id)->exists()) {
            return success_response("You already unlocked this lead.", new LeadUserResource($lead->user));
        }

        // Consume 1 credit using your original service
        try {
            $creditType = $this->creditService->consumeOne($business->id); // 'free' or 'paid'
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'NO_CREDITS') {
                $balance = $this->creditService->balances($business->id);
                $wallet = $business->creditWallet; // assuming relation exists

                $message = "You don’t have enough credits. Free credits left: {$balance['free_remaining']}, Paid credits left: {$balance['paid_credits']}.";

                if ($balance['paid_credits'] > 0 && $wallet && $wallet->expire_date && $wallet->expire_date->isPast()) {
                    $message .= " Your paid credits expired on " . $wallet->expire_date->format('d M Y') . ".";
                }

                return error_response($message);
            }
            throw $e;
        }

        // Save unlock record
        BusinessLead::create([
            'business_id' => $business->id,
            'lead_id'     => $lead->id,
        ]);

        return success_response("Lead unlocked successfully using {$creditType} credit", new LeadUserResource($lead->user));
    }

    // Show all unlocked leads
    public function myUnlocked(Request $request)
    {
        $business = auth()->user()->business;

        // Default pagination values
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $leads = $business->business_leads()
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return success_response('Unlocked leads fetched successfully', [
            'data' => BusinessLeadResource::collection($leads),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ]
        ]);
    }
}
