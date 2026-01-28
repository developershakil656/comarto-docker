<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountVerificationResource;
use App\Models\AccountVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountVerificationController extends Controller
{
    /**
     * GET: Account verifications list
     */
    public function index(Request $request)
    {
        $verifications = AccountVerification::query()
            ->when($request->user_id, fn ($q) =>
                $q->where('user_id', $request->user_id)
            )
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate($request->get('per_page', 10));

        return success_response(
            'Account verifications fetched successfully',
            [
                'data' => AccountVerificationResource::collection($verifications),
                'meta' => [
                    'current_page' => $verifications->currentPage(),
                    'last_page'    => $verifications->lastPage(),
                    'per_page'     => $verifications->perPage(),
                    'total'        => $verifications->total(),
                ],
            ]
        );
    }

    /**
     * PUT: Update verification status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:in review,confirmed,rejected',
        ]);

        if ($validator->fails()) {
            return error_response(
                'Validation failed',
                $validator->errors()
            );
        }

        $verification = AccountVerification::find($id);

        if (!$verification) {
            return error_response('Account verification not found', null, 404);
        }

        $verification->update([
            'status' => $request->status,
        ]);

        return success_response('Account verification status updated successfully');
    }
}