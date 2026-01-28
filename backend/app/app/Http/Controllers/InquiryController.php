<?php

namespace App\Http\Controllers;

use App\Http\Resources\InquiryResource;
use App\Models\Lead;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Validator;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Default pagination values
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $inquiries = Lead::where('user_id', $user->id)
            ->with(['category', 'location']) // optional: load relations
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return success_response('Your inquiries fetched successfully', [
            'data' => InquiryResource::collection($inquiries),
            'meta' => [
                'current_page' => $inquiries->currentPage(),
                'last_page' => $inquiries->lastPage(),
                'per_page' => $inquiries->perPage(),
                'total' => $inquiries->total(),
            ]
        ]);
    }


    // Create a lead
    public function store(Request $request)
    {
        // Decode hashid fields before validation
        $decodedCategoryId = Hashids::decode($request->category_id);
        $decodedLocationId = Hashids::decode($request->location_id);

        // Ensure decoded values exist and are valid integers
        if (empty($decodedCategoryId) || empty($decodedLocationId)) {
            return error_response('Invalid category or location ID.');
        }

        // Replace request data with decoded IDs for validation and saving
        $request->merge([
            'category_id' => $decodedCategoryId[0],
            'location_id' => $decodedLocationId[0],
        ]);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'quantity'    => 'nullable|integer|min:1',
            'unit_name'    => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return error_response('Validation failed!', $validator->errors());
        }

        $lead = Lead::create([
            'user_id'     => auth()->id(),
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'quantity'    => $request->quantity,
            'unit_name'    => $request->unit_name,
            'description' => $request->description ?? null,
            'status' => 'open',
        ]);

        return success_response('Lead created successfully', new InquiryResource($lead), 201);
    }

    // Close a lead
    public function close($id)
    {
        $id = $id ? Hashids::decode($id)[0] : [];
        $lead = Lead::findOrFail($id);

        if ($lead->user_id != auth()->id()) {
            return error_response('Unauthorized to close this lead');
        }

        $lead->update(['status' => 'closed']);
        return success_response('Lead closed successfully');
    }
}
