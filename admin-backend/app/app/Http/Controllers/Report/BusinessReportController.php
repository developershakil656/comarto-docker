<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessRequest;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\BusinessDetail;
use App\Models\User;
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
                    ->orWhere('number', 'LIKE', "%$keyword%")
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

        $businesses = $query->latest()->paginate($perPage, ['*'], 'page', $page);

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

    public function show($id)
    {
        $business = Business::with('details', 'location', 'user')->find($id);
        
        if (!$business) {
            return error_response('Business not found');
        }

        return success_response('Business fetched successfully', new BusinessResource($business));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessRequest $request)
    {
        $user = User::firstOrCreate(['number' => $request->number], ['name' => $request?->name, 'email' => $request?->email]);

        if ($user->business) {
            return error_response('This user has already created a business!');
        }

        $request['user_id'] = $user->id;
        if ($data = Business::create($request->except('timings', 'business_profile'))) {
            BusinessDetail::create(['business_id' => $data->id, 'timing' => ($request->timings ?? null)]);
            
            if ($request->hasFile('business_profile')) {
                $file_url = image_link_generator(
                    $request->file('business_profile'),
                    '/business/profile/',
                    $data->business_name,
                    300
                );

                // Save new file URL
                $data->business_profile = $file_url;
                $data->save();
            }
            return success_response('business successfully created', new BusinessResource($data));
        }
        
        return error_response('something went wrong!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessRequest $request, string $id)
    {
        $data = Business::find($id);
        if ($data) {
            // $data->update(array_filter(
            //     $request->all(),
            //     fn($value) => !is_null($value) && $value !== ''
            // ));
            $data->update($request->except('business_profile'));
            if ($request->hasFile('business_profile')) {
                // Delete old image if exists
                delete_image($data->business_profile);

                // Upload new image
                $file_url = image_link_generator(
                    $request->file('business_profile'),
                    '/business/profile/',
                    $data->business_name,
                    300
                );

                // Save new file URL
                $data->business_profile = $file_url;
                $data->save();
            }
            return success_response('business successfully updated', new BusinessResource($data));
        } else
            return error_response('No Data Found!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the business record associated with the user
        $data = Business::find($id);

        // Check if business exists and belongs to the logged-in user
        if ($data) {
            // Attempt to delete the business
            if ($data->delete()) {
                
                // Delete the business profile image if it exists
                if ($data->business_profile) {
                    // Handle image deletion - check both admin-backend and backend storage paths
                    delete_image($data->business_profile);
                }

                return success_response('Business successfully deleted');
            } else {
                return error_response('Failed to delete business', [], 500);
            }
        } else {
            return error_response('No business found for this user!', [], 404);
        }
    }

}
