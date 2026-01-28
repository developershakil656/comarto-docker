<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadCreditPackageResource;
use App\Http\Resources\LeadCreditPurchaseResource;
use App\Models\LeadCreditPackage;
use App\Models\LeadCreditPurchase;
use App\Services\LeadCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class LeadCreditPackageController extends Controller
{
    protected $creditService;

    public function __construct(LeadCreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /** Show all active packages */
    public function packages()
    {
        $packages = LeadCreditPackage::where('is_active', true)->get();
        return success_response('Lead credit packages fetched successfully', LeadCreditPackageResource::collection($packages));
    }

    /** Purchase a package */
    public function purchase(Request $request)
    {
        // Decode hashid fields before validation
        $decodedPackageId = Hashids::decode($request->package_id);

        // Ensure decoded values exist and are valid integers
        if (empty($decodedPackageId)) {
            return error_response('Invalid Package.');
        }

        // Replace request data with decoded IDs for validation and saving
        $request->merge([
            'package_id' => $decodedPackageId[0],
        ]);

        $validator = Validator::make($request->all(), [
            'package_id'     => 'required|exists:lead_credit_packages,id',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return error_response('Validation failed!', $validator->errors());
        }

        $businessId = auth()->user()->business->id;

        try {
            DB::transaction(function () use ($request, $businessId) {
                $package = LeadCreditPackage::findOrFail($request->package_id);

                // Add credits to wallet with package duration
                // $this->creditService->addPaid($businessId, $package->credits, $package->duration_months);

                // Save purchase history
                LeadCreditPurchase::create([
                    'business_id'    => $businessId,
                    'package_id'     => $package->id,
                    'credits'        => $package->credits,
                    'amount'         => $package->price,
                    // 'currency'       => $package->currency,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'status'         => 'pending',
                    'expiry_date'    => now()->addMonths($package->duration_months),
                ]);
            });

            // $balances = $this->creditService->balances($businessId);
            return success_response('Lead credits purchased successfully');

        } catch (\Exception $e) {
            return error_response('Purchase failed!', ['error' => $e->getMessage()]);
        }
    }

    public function purchases()
    {
        $businessId = auth()->user()->business->id;
        $history = LeadCreditPurchase::where('business_id', $businessId)
            ->latest()
            ->get();

        return success_response('Purchase history fetched', LeadCreditPurchaseResource::collection($history));
    }
}