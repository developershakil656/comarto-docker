<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadCreditPurchaseResource;
use App\Models\LeadCreditPurchase;
use App\Services\LeadCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadCreditPurchaseController extends Controller
{
    protected $creditService;

    public function __construct(LeadCreditService $creditService)
    {
        $this->creditService = $creditService;
    }
    /**
     * GET: Lead credit purchases with filters
     */
    public function index(Request $request)
    {
        $purchases = LeadCreditPurchase::query()
            ->when($request->business_id, fn ($q) =>
                $q->where('business_id', $request->business_id)
            )
            ->when($request->package_id, fn ($q) =>
                $q->where('package_id', $request->package_id)
            )
            ->when($request->transaction_id, fn ($q) =>
                $q->where('transaction_id', 'like', '%' . $request->transaction_id . '%')
            )
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->expire_date, fn ($q) =>
                $q->whereDate('expire_date', $request->expire_date)
            )
            ->when($request->start_date && $request->end_date, fn ($q) =>
                $q->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date
                ])
            )
            ->latest()
            ->paginate($request->get('per_page', 10));

        return success_response(
            'Lead credit purchases fetched successfully',
            [
                'data' => LeadCreditPurchaseResource::collection($purchases),
                'meta' => [
                    'current_page' => $purchases->currentPage(),
                    'last_page'    => $purchases->lastPage(),
                    'per_page'     => $purchases->perPage(),
                    'total'        => $purchases->total(),
                ],
            ]
        );
    }

    /**
     * PUT: Update purchase status only
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return error_response(
                'Validation failed',
                $validator->errors()
            );
        }

        $purchase = LeadCreditPurchase::find($id);

        if (!$purchase) {
            return error_response('Lead credit purchase not found', null, 404);
        }

        if ($request->status === 'completed') {
            try {
                $this->creditService->addPaidCredits($purchase->business_id, $purchase->credits, $purchase->package->duration_months);
            } catch (\Exception $e) {
                return error_response($e->getMessage(), null, 500);
            }
        }
        $purchase->update([
            'status' => $request->status,
        ]);

        return success_response(
            'Purchase status updated successfully',
            new LeadCreditPurchaseResource($purchase)
        );
    }
}
