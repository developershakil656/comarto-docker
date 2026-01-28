<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadCreditPackageResource;
use App\Models\LeadCreditPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadCreditPackageController extends Controller
{
    /**
     * List packages
     */
    public function index(Request $request)
    {
        $packages = LeadCreditPackage::query()
            ->when($request->filled('is_active'), function ($q) use ($request) {
                $q->where('is_active', $request->boolean('is_active'));
            })
            ->paginate($request->get('per_page', 10));

        return success_response(
            'Lead credit packages fetched successfully',
            [
                'data' => LeadCreditPackageResource::collection($packages),
                'meta' => [
                    'current_page' => $packages->currentPage(),
                    'last_page'    => $packages->lastPage(),
                    'per_page'     => $packages->perPage(),
                    'total'        => $packages->total(),
                ],
            ]
        );
    }

    /**
     * Store new package
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'credits'         => 'required|integer|min:1',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
            'duration_months' => 'nullable|integer|min:1',
            'is_active'       => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return error_response(
                'Validation failed',
                $validator->errors()
            );
        }

        $package = LeadCreditPackage::create($validator->validated());

        return success_response(
            'Lead credit package created successfully',
            new LeadCreditPackageResource($package)
        );
    }

    /**
     * Show single package
     */
    public function show(LeadCreditPackage $leadCreditPackage)
    {
        return success_response(
            'Lead credit package details',
            new LeadCreditPackageResource($leadCreditPackage)
        );
    }

    /**
     * Update package
     */
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'sometimes|string|max:255',
            'credits'         => 'sometimes|integer|min:1',
            'price'           => 'sometimes|numeric|min:0',
            'currency'        => 'sometimes|string|max:10',
            'duration_months' => 'sometimes|integer|min:1',
            'is_active'       => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return error_response(
                'Validation failed',
                $validator->errors()
            );
        }

        $package = LeadCreditPackage::find($id);

        if (!$package) {
            return error_response('Package not found', null, 404);
        }

        // Update package
        $package->update($request->all());

        // Return updated model in resource
        return success_response(
            'Lead credit package updated successfully',
            new LeadCreditPackageResource($package)
        );
    }

    /**
     * Delete package
     */
    public function destroy($id)
    {
        $package = LeadCreditPackage::find($id);

        if (!$package) {
            return error_response('Package not found', null, 404);
        }
        if($package->name === 'Quick'){
            return success_response('Quick package cannot be deleted', null, 400);
        }
        $package->delete();
        return success_response('Lead credit package deleted successfully');
    }
}
